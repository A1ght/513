CREATE DATABASE UserDB;
USE UserDB;
 
CREATE TABLE customers (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL, -- 应存储哈希值
    phone VARCHAR(20),
    email VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'customer' -- 默认角色为 'customer'
);
 
-- 插入示例数据（注意：这里的密码哈希值只是示例，实际中应使用安全的哈希算法生成）
INSERT INTO customers (username, password, phone, email, role) VALUES
('exampleUser', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1234567890', 'example@example.com', 'customer'),
('adminUser', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0987654321', 'admin@example.com', 'admin');
 
-- 创建产品表
CREATE TABLE Products (
    ProductID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Price DECIMAL(10, 2) NOT NULL,
    Description TEXT,
    ImageURL VARCHAR(255)
);
 
-- 创建 orders 表
CREATE TABLE orders (
    order_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    customer_id INTEGER NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
);
 
-- 创建 order_details 表
-- 注意：这里的FOREIGN KEY (order_id) REFERENCES orders(order_id)应该是FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE（或其他适当的操作），
-- 但这取决于你的业务需求。如果省略ON DELETE部分，MySQL将不允许你创建这样的外键，因为它可能导致孤立记录。然而，在这个修正中，我仅更正了语法错误。
CREATE TABLE order_details (
    order_detail_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    order_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES Products(ProductID) -- 注意这里引用的是Products表的ProductID字段
);
 
-- 创建support_messages表
CREATE TABLE support_messages (
    id INT NOT NULL AUTO_INCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL,
    info TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);
 
-- 创建career_applications表
CREATE TABLE career_applications (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name TEXT NOT NULL,
    number TEXT NOT NULL, -- 注意：通常电话号码字段可能会使用VARCHAR(15)或类似的长度限制
    email TEXT NOT NULL,
    position TEXT NOT NULL,
    gender TEXT NOT NULL, -- 注意：存储性别为文本可能不是最佳实践，考虑使用ENUM类型或单独的性别表
    file1 TEXT NOT NULL,
    file2 TEXT NOT NULL
);