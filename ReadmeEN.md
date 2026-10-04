# Zoo Management System (SAE Project)

This project is a comprehensive Zoo Management System. Built with PHP and backed by an Oracle database, the application centralizes the park's administration. It features a secure, session-based authentication system that tailors the interface and modification rights according to the employee's role: HR and global statistics for accountants, medical and dietary tracking for zookeepers, technical maintenance of enclosures, and sales tracking for shop managers.

---

## 1. Access & Credentials (Role-Based Testing)

All test accounts use the password: **`admin1`**[cite: 11].
Passwords in the database are secured using `password_hash()`[cite: 11].

### A. LEVEL: ACCOUNTANT (Admin / HR)
- **ID:** `507` (Nina Leroy)
- **Permissions:** Full management of staff, archiving, contracts, animals, and global statistics overview.

### B. LEVEL: SHOP MANAGER
- **ID:** `506` (Sophie Martin)[cite: 11]
- **Permissions:** Entry and consultation of Sales Revenue (CA), sponsor search, and access to the visitor directory[cite: 11].

### C. LEVEL: ZOOKEEPER
- **IDs:** `500` (Paul Durand) / `501` (Claire Lemoine)[cite: 11]
- **Permissions:** Access to the "Care Tour", food management, and medical tracking of assigned animals[cite: 11].

### D. LEVEL: MAINTENANCE (Technical)
- **ID:** `505` (Luc Bernard)[cite: 11]
- **Permissions:** Reporting repairs and tracking enclosure status[cite: 11].

---

## 2. Technical Details & Design Choices

- **Security:** PHP sessions are used to restrict access to specific pages[cite: 11]. A user cannot access functions belonging to a role higher than their own[cite: 11].
- **Database:** The schema adheres to normal forms to prevent data redundancy[cite: 11]. Foreign keys ensure referential integrity (e.g., it is impossible to delete an enclosure if it contains animals)[cite: 11].
- **SQL Queries:** Data retrieval is optimized using explicit joins (`JOIN...ON`) for complex structures and implicit joins (`WHERE A.id = B.id`) for simple associations[cite: 11]. This approach guarantees accuracy when cross-referencing data between staff, zones, and animals[cite: 11].

![alt text]({F9BE735D-104A-41ED-A1B8-BC1C66BF8A01}.png)

---

## 3. Installation & Deployment

1. Run the `bd.sql` file in an Oracle environment to create tables and insert the test mock data[cite: 11].
2. Place the contents of the `public_html` folder onto your web server[cite: 11].
3. Configure your database credentials (Host, User, Pass) in the `myparam.inc.example.php` file[cite: 11].
4. To reset the database, execute `drop.sql`[cite: 11].
