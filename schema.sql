CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    salt VARCHAR(255), -- Optional for additional security
    role ENUM('Admin', 'HOD', 'Security', 'GateMan') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dep_name VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE gate_pass (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pass_no VARCHAR(20) UNIQUE NOT NULL,
    date DATE NOT NULL,
    taken_by VARCHAR(100) NOT NULL,
    company VARCHAR(100) NOT NULL,
    department_id INT NOT NULL,
    reason TEXT NOT NULL,
    expected_return_date DATE,
    approved_by INT NULL, -- References users(id) when approved
    status ENUM('Pending', 'Approved', 'Checked Out', 'Checked In') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE RESTRICT,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gate_pass_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL CHECK (quantity > 0),
    serial_no VARCHAR(50) DEFAULT NULL,
    returnable ENUM('Yes', 'No') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (gate_pass_id) REFERENCES gate_pass(id) ON DELETE CASCADE
);

CREATE TABLE gate_passes_departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gate_pass_id INT NOT NULL,
    dep_id INT NOT NULL,
    FOREIGN KEY (gate_pass_id) REFERENCES gate_pass(id) ON DELETE CASCADE,
    FOREIGN KEY (dep_id) REFERENCES departments(id) ON DELETE CASCADE
);
