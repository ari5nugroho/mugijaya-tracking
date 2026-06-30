<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ChecklistPhoto;
use App\Models\DeliveryChecklist;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DeliveryChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kepalaLapangan = User::where('email', 'kepalalapangan@mugijaya.com')->first();

        $mandorByWarehouse = User::role('Mandor')->get()->keyBy('warehouse_id');

        // Peta status order -> apa yang harus terjadi di checklist
        // pending                   -> belum ada checklist sama sekali
        // checklist_mandor          -> checklist layer mandor: pending
        // checklist_kepala_lapangan -> checklist layer mandor: approved, layer kepala_lapangan: pending
        // shipped / delivered       -> kedua layer: approved
        // cancelled                 -> tidak ada checklist

        $orders = Order::whereIn('order_number', ['ORD-2026-0002', 'ORD-2026-0003', 'ORD-2026-0004', 'ORD-2026-0005'])->get();

        foreach ($orders as $order) {
            $mandor = $mandorByWarehouse->get($order->warehouse_id);

            $mandorStatus = 'pending';
            $kepalaLapanganStatus = 'pending';

            if ($order->status === 'checklist_mandor') {
                $mandorStatus = 'pending';
            } elseif (in_array($order->status, ['checklist_kepala_lapangan', 'shipped', 'delivered'])) {
                $mandorStatus = 'approved';
            }

            if (in_array($order->status, ['shipped', 'delivered'])) {
                $kepalaLapanganStatus = 'approved';
            }

            // --- Checklist Layer 1: Mandor ---
            $checklistMandor = DeliveryChecklist::updateOrCreate(
                ['order_id' => $order->id, 'layer' => 'mandor'],
                [
                    'status' => $mandorStatus,
                    'checked_by' => $mandor?->id,
                    'checked_at' => $mandorStatus === 'approved' ? Carbon::parse($order->order_date)->addHours(2) : null,
                    'notes' => $mandorStatus === 'approved' ? 'Barang sudah dicek sesuai pesanan, kondisi baik.' : 'Menunggu pengecekan fisik barang oleh Mandor.',
                ],
            );

            if ($mandorStatus === 'approved') {
                ChecklistPhoto::firstOrCreate(
                    [
                        'delivery_checklist_id' => $checklistMandor->id,
                        'photo_path' => 'checklist/mandor/' . $order->order_number . '-01.jpg',
                    ],
                    [
                        'caption' => 'Foto kondisi barang sebelum dimuat ke kendaraan.',
                    ],
                );
            }

            // --- Checklist Layer 2: Kepala Lapangan ---
            // Hanya dibuat kalau order sudah lewat tahap checklist_mandor
            if (in_array($order->status, ['checklist_kepala_lapangan', 'shipped', 'delivered'])) {
                $checklistKepalaLapangan = DeliveryChecklist::updateOrCreate(
                    ['order_id' => $order->id, 'layer' => 'kepala_lapangan'],
                    [
                        'status' => $kepalaLapanganStatus,
                        'checked_by' => $kepalaLapangan?->id,
                        'checked_at' => $kepalaLapanganStatus === 'approved' ? Carbon::parse($order->order_date)->addHours(5) : null,
                        'notes' => $kepalaLapanganStatus === 'approved' ? 'Disetujui untuk dikirim, dokumen dan muatan lengkap.' : 'Menunggu approval akhir sebelum keberangkatan.',
                    ],
                );

                if ($kepalaLapanganStatus === 'approved') {
                    ChecklistPhoto::firstOrCreate(
                        [
                            'delivery_checklist_id' => $checklistKepalaLapangan->id,
                            'photo_path' => 'checklist/kepala-lapangan/' . $order->order_number . '-01.jpg',
                        ],
                        [
                            'caption' => 'Foto muatan siap dikirim, sudah diverifikasi.',
                        ],
                    );
                }
            }
        }

        $this->command->info('✅ Dummy delivery checklists & foto berhasil dibuat!');
    }
}
