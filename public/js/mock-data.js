// mock-data.js - Simulated Database using localStorage

const INITIAL_USERS = [
    { id: 1, name: "Budi Santoso", email: "budi.admin@mugijaya.com", role: "Super Admin", status: "Active" },
    { id: 2, name: "Siti Rahma", email: "siti.warehouse@mugijaya.com", role: "Warehouse Admin", status: "Active" },
    { id: 3, name: "Agus Pratama", email: "agus.courier@mugijaya.com", role: "Courier Admin", status: "Active" },
    { id: 4, name: "Rian Hidayat", email: "rian.driver@mugijaya.com", role: "Driver", status: "Active" },
    { id: 5, name: "Dewi Lestari", email: "dewi.qc@mugijaya.com", role: "Validator", status: "Active" }
];

const INITIAL_WAREHOUSES = [
    { id: 1, code: "WH-SMG-01", name: "Gudang Utama Semarang", manager: "Siti Rahma", capacity: 50000, capacityUsed: 38500, address: "Jl. Kaligawe Raya No.12, Semarang" },
    { id: 2, code: "WH-JKT-02", name: "Gudang Transit Jakarta", manager: "Hendra Wijaya", capacity: 30000, capacityUsed: 14200, address: "Kawasan Industri Pulogadung Blok C, Jakarta" },
    { id: 3, code: "WH-SBY-03", name: "Gudang Transit Surabaya", manager: "Ahmad Fauzi", capacity: 25000, capacityUsed: 22800, address: "Jl. Margomulyo No.45, Surabaya" }
];

const INITIAL_PRODUCTS = [
    { id: 1, sku: "PRD-ELC-001", name: "Smart TV LED 43 Inch", category: "Electronics", weight: 8.5, unit: "Pcs", price: 3500000 },
    { id: 2, sku: "PRD-FNT-023", name: "Kursi Kantor Ergonomis", category: "Furniture", weight: 12.0, unit: "Pcs", price: 1200000 },
    { id: 3, sku: "PRD-FNB-102", name: "Kopi Arabika Java Blend 1Kg", category: "Food & Beverage", weight: 1.0, unit: "Pack", price: 150000 },
    { id: 4, sku: "PRD-CLT-055", name: "Kaos Polo Cotton Combed XL", category: "Apparel", weight: 0.25, unit: "Pcs", price: 85000 },
    { id: 5, sku: "PRD-HSH-089", name: "Pembersih Udara HEPA Filter", category: "Household", weight: 4.2, unit: "Pcs", price: 1800000 }
];

const INITIAL_STOCKS = [
    { id: 1, sku: "PRD-ELC-001", warehouseId: 1, stockCurrent: 120, stockMin: 20 },
    { id: 2, sku: "PRD-ELC-001", warehouseId: 2, stockCurrent: 15, stockMin: 10 },
    { id: 3, sku: "PRD-FNT-023", warehouseId: 1, stockCurrent: 45, stockMin: 15 },
    { id: 4, sku: "PRD-FNT-023", warehouseId: 3, stockCurrent: 8, stockMin: 10 }, // Low stock!
    { id: 5, sku: "PRD-FNB-102", warehouseId: 1, stockCurrent: 350, stockMin: 50 },
    { id: 6, sku: "PRD-FNB-102", warehouseId: 2, stockCurrent: 120, stockMin: 50 },
    { id: 7, sku: "PRD-CLT-055", warehouseId: 3, stockCurrent: 400, stockMin: 100 },
    { id: 8, sku: "PRD-HSH-089", warehouseId: 1, stockCurrent: 5, stockMin: 8 },  // Low stock!
    { id: 9, sku: "PRD-HSH-089", warehouseId: 2, stockCurrent: 22, stockMin: 5 }
];

const INITIAL_DRIVERS = [
    { id: 1, name: "Rian Hidayat", phone: "081234567890", licensePlate: "H 1234 AB", vehicleClass: "CDE Box (Medium)", status: "On Delivery", rating: 4.8 },
    { id: 2, name: "Joko Susilo", phone: "082345678901", licensePlate: "H 8765 XY", vehicleClass: "CDD Box (Heavy)", status: "Available", rating: 4.9 },
    { id: 3, name: "Wawan Hermawan", phone: "083456789012", licensePlate: "B 9911 CDE", vehicleClass: "Wingbox Truck", status: "Available", rating: 4.7 },
    { id: 4, name: "Denny Setiawan", phone: "084567890123", licensePlate: "L 4321 FG", vehicleClass: "Pickup Bak (Light)", status: "Break", rating: 4.5 },
    { id: 5, name: "Eko Prasetyo", phone: "085678901234", licensePlate: "H 5678 CD", vehicleClass: "CDE Box (Medium)", status: "Off Duty", rating: 4.6 }
];

const INITIAL_DELIVERY_ORDERS = [
    {
        id: 1,
        doNumber: "DO-2026-0001",
        originId: 1,
        destination: "Toko Elektronik Makmur, Jl. Pemuda No.10, Semarang",
        driverId: 1,
        status: "In Transit",
        date: "2026-06-15",
        items: [
            { sku: "PRD-ELC-001", quantity: 10 },
            { sku: "PRD-HSH-089", quantity: 2 }
        ],
        validationStatus: "Validated",
        validationNotes: "Segel aman, jumlah pas.",
        pod: null
    },
    {
        id: 2,
        doNumber: "DO-2026-0002",
        originId: 1,
        destination: "UD Jaya Furniture, Jl. Slamet Riyadi No.150, Solo",
        driverId: 2,
        status: "Delivered",
        date: "2026-06-14",
        items: [
            { sku: "PRD-FNT-023", quantity: 8 }
        ],
        validationStatus: "Validated",
        validationNotes: "Tidak ada cacat.",
        pod: {
            signedBy: "Pak Slamet (Owner)",
            signedAt: "2026-06-14T15:30:00Z",
            signatureImage: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABGBAMAAAA8X8fBAAAAFVBMVEUAAAD///8AAAAAAAAAAAAAAAAAAAAsF28BAAAABnRSTlMA8vL39/fl6t2EAAAAWElEQVRYR+3QsQ3AIBBEUX6oEEgZJsgm2SjZICMkdF3XwV0u/iVz4A8W5m1WKK4q1KpqY/c7O1p1j20Y1R+G9cOhj7rG79l3jWqWd75Z1P1xHw/n7m73+GZRhVpVgAcKCRtL2u3QAAAAAElFTkSuQmCC", // Dummy sig
            photoUrl: "https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=300&q=80"
        }
    },
    {
        id: 3,
        doNumber: "DO-2026-0003",
        originId: 3,
        destination: "Koperasi Pegawai Surabaya, Jl. Ahmad Yani No.5, Surabaya",
        driverId: null,
        status: "Pending Validation",
        date: "2026-06-15",
        items: [
            { sku: "PRD-CLT-055", quantity: 150 },
            { sku: "PRD-FNB-102", quantity: 50 }
        ],
        validationStatus: "Pending QC",
        validationNotes: "",
        pod: null
    },
    {
        id: 4,
        doNumber: "DO-2026-0004",
        originId: 2,
        destination: "PT Central Retail, Kawasan Sudirman, Jakarta",
        driverId: 3,
        status: "Prepared",
        date: "2026-06-15",
        items: [
            { sku: "PRD-HSH-089", quantity: 15 }
        ],
        validationStatus: "Validated",
        validationNotes: "Barang siap kirim.",
        pod: null
    }
];

const INITIAL_AUDIT_LOGS = [
    { id: 1, timestamp: "2026-06-15T11:20:00+07:00", user: "Siti Rahma", action: "Stock Update", type: "Stock In", details: "Menambahkan 50 Pcs Kopi Arabika di WH-SMG-01", status: "Success" },
    { id: 2, timestamp: "2026-06-15T10:45:00+07:00", user: "Budi Santoso", action: "User Access", type: "Login", details: "Admin budi.admin@mugijaya.com berhasil login", status: "Success" },
    { id: 3, timestamp: "2026-06-15T09:15:00+07:00", user: "Agus Pratama", action: "Delivery Assignment", type: "Assign", details: "Menugaskan DO-2026-0001 kepada Rian Hidayat", status: "Success" },
    { id: 4, timestamp: "2026-06-15T08:30:00+07:00", user: "Dewi Lestari", action: "QC Validation", type: "QC Check", details: "Memvalidasi DO-2026-0002 tanpa temuan cacat", status: "Success" },
    { id: 5, timestamp: "2026-06-14T16:10:00+07:00", user: "Rian Hidayat", action: "Proof of Delivery", type: "POD Upload", details: "Mengunggah bukti pengiriman DO-2026-0002", status: "Success" }
];

const INITIAL_INCIDENTS = [
    { id: 1, doNumber: "DO-2026-0001", reporter: "Rian Hidayat", date: "2026-06-15", type: "Kecelakaan Ringan", severity: "Medium", description: "Ban truk bocor di Tol Trans Jawa KM 200, pengiriman tertunda 45 menit.", status: "Resolved", resolution: "Ban diganti dengan ban cadangan oleh driver, perjalanan dilanjutkan." },
    { id: 2, doNumber: "DO-2026-0002", reporter: "Joko Susilo", date: "2026-06-14", type: "Cuaca Buruk", severity: "Low", description: "Hujan lebat disertai angin kencang di Solo, berteduh sementara di rest area.", status: "Resolved", resolution: "Melanjutkan pengiriman setelah cuaca membaik." }
];

// Database operations class
class ErpDatabase {
    constructor() {
        this.initKey('users', INITIAL_USERS);
        this.initKey('warehouses', INITIAL_WAREHOUSES);
        this.initKey('products', INITIAL_PRODUCTS);
        this.initKey('stocks', INITIAL_STOCKS);
        this.initKey('drivers', INITIAL_DRIVERS);
        this.initKey('deliveryOrders', INITIAL_DELIVERY_ORDERS);
        this.initKey('auditLogs', INITIAL_AUDIT_LOGS);
        this.initKey('incidents', INITIAL_INCIDENTS);
    }

    initKey(key, initialData) {
        if (!localStorage.getItem(`erp_${key}`)) {
            localStorage.setItem(`erp_${key}`, JSON.stringify(initialData));
        }
    }

    getData(key) {
        return JSON.parse(localStorage.getItem(`erp_${key}`));
    }

    setData(key, data) {
        localStorage.setItem(`erp_${key}`, JSON.stringify(data));
        return data;
    }

    // Generic inserts
    insertItem(key, item) {
        const data = this.getData(key);
        item.id = data.length > 0 ? Math.max(...data.map(d => d.id)) + 1 : 1;
        data.unshift(item); // Add to beginning
        this.setData(key, data);
        
        // Log action automatically
        this.logAction(
            "System",
            "Data Insertion",
            `Insert`,
            `Menambahkan data baru di tabel ${key} (ID: ${item.id})`,
            "Success"
        );
        return item;
    }

    updateItem(key, id, updatedFields) {
        const data = this.getData(key);
        const index = data.findIndex(d => d.id === parseInt(id));
        if (index !== -1) {
            data[index] = { ...data[index], ...updatedFields };
            this.setData(key, data);
            
            this.logAction(
                "System",
                "Data Modification",
                `Update`,
                `Mengubah data di tabel ${key} (ID: ${id})`,
                "Success"
            );
            return data[index];
        }
        return null;
    }

    deleteItem(key, id) {
        let data = this.getData(key);
        data = data.filter(d => d.id !== parseInt(id));
        this.setData(key, data);
        
        this.logAction(
            "System",
            "Data Removal",
            `Delete`,
            `Menghapus data di tabel ${key} (ID: ${id})`,
            "Success"
        );
    }

    logAction(user, action, type, details, status = "Success") {
        const logs = this.getData('auditLogs');
        const newLog = {
            id: logs.length > 0 ? Math.max(...logs.map(l => l.id)) + 1 : 1,
            timestamp: new Date().toISOString(),
            user,
            action,
            type,
            details,
            status
        };
        logs.unshift(newLog);
        this.setData('auditLogs', logs);
    }
}

// Instantiate globally
const db = new ErpDatabase();
