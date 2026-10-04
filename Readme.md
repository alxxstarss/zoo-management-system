# ZOO MANAGEMENT SYSTEM

## 1. PROJECT CONTEXT

This project was developed as part of a SAE focused on the design and development of a management system for a fictional zoo, **Zoo'Land**.

The goal of the project is to design an application that centralizes and manages the zoo's various activities through an Oracle database and a web interface.

The system notably manages:

* Animals and their species
* Enclosures and the different areas of the zoo
* Keepers and animal care
* Animal feeding
* Veterinarians and complex medical treatments
* Staff members and their employment history
* Repairs and enclosure maintenance
* Shops and their revenue
* Visitors and animal sponsorships
* Access rights and roles for the different staff members

The web interface allows each staff member to access only the features corresponding to their role and responsibilities.

The project therefore focuses on **data management**, **access security**, **database integrity**, and **user access rights management**.

The database was designed based on a **Conceptual Data Model (CDM)** created beforehand as part of the Database section of the project.

The CDM represents the main entities of the system, as well as their relationships and constraints. It served as a reference during the design and implementation of the Oracle database.

<img width="1897" height="816" alt="{268A1AD8-12F5-437C-ADB4-ABECA8BE34F3}" src="https://github.com/user-attachments/assets/53781a9f-f7df-4894-b945-ce4dff9a078f" />

---

## 2. ACCESS AND TEST CREDENTIALS

All test accounts use the following password:

**admin1**

Passwords are stored in the database as hashes generated using `password_hash()`.

### A. ROLE: ACCOUNTANT (Administrator / HR)

* ID: `507` (Nina Leroy)
* Permissions: Full management of staff, archiving, contracts, animals, and global statistics.

### B. ROLE: SHOP MANAGER

* ID: `506` (Sophie Martin)
* Permissions: Entering and viewing revenue, searching for sponsors, and accessing the visitor directory.

### C. ROLE: ANIMAL KEEPER

* IDs: `500` (Paul Durand) / `501` (Claire Lemoine)
* Permissions: Viewing the "Care Schedule", managing animal feeding, and monitoring the medical status of assigned animals.

### D. ROLE: MAINTENANCE (Technical Staff)

* ID: `505` (Luc Bernard)
* Permissions: Reporting repairs and monitoring the condition of enclosures.

---

## 3. TECHNICAL EXPLANATIONS & DESIGN CHOICES

### Security

The application uses **PHP sessions** to track the logged-in user and restrict access to different pages and features.

Permissions are checked according to the user's role. A user cannot access features reserved for a higher-level role.

Passwords are never stored in plain text and are secured using `password_hash()`.

### Database

The database schema follows normalization principles in order to reduce redundancy and ensure data consistency.

Foreign keys are used to maintain referential integrity between the different tables.

For example, deleting an enclosure that still contains animals is prevented in order to avoid creating inconsistent data.

### SQL Queries

The application uses various SQL queries to search, filter, and cross-reference information from multiple tables.

**Explicit joins (`JOIN ... ON`)** are used for more complex structures, while implicit joins (`WHERE A.id = B.id`) may be used for simpler relationships.

This approach ensures accurate retrieval of information related to staff, areas, animals, medical care, and other elements of the system.

### Data Validation

User-submitted data is validated in order to limit errors and inconsistencies.

Particular attention is also paid to the security of user inputs in order to reduce risks related to SQL injection and HTML code injection.

---

## 4. INSTALLATION AND DEPLOYMENT

### Database

1. Execute the `bd.sql` file in the Oracle environment to create the tables and insert the test data.

2. Configure the database connection parameters in:

```text
myparam.inc.example.php
```

### Web Interface

3. Place the contents of the `public_html` directory on the web server.

4. Make sure that the server has PHP installed and can connect to the Oracle database.

### Reset

If necessary, the database can be reset by executing:

```text
drop.sql
```

---

## 5. MAIN FEATURES

The system notably allows users to:

* Authenticate zoo staff members
* Manage user sessions
* Control access rights according to user roles
* View and search database information
* Add, edit, and delete certain data
* Manage animals and their information
* Track medical care performed on animals
* Manage animal feeding
* Track enclosure repairs
* Manage shops and their revenue
* View visitor and sponsorship information
* View statistics according to user permissions
* Manage staff information and employment history

---

## 6. VIDEO DEMONSTRATION

A demonstration video of the website is provided with the project in **MP4** format.

To view the complete operation of the application and discover the different features available according to each role, simply open the following file:

```text
ZOOLAND.mp4
```

**Duration: 6 min 13 sec**

The video demonstrates the login process for the different users, access rights management, data consultation, as well as the main features of the application.
