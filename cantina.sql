CREATE TABLE orders (
    id varchar(30) PRIMARY KEY,
    user_id varchar(30) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    order_date DATETIME NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_status ENUM('completed', 'pending') NOT NULL,
    shipping_address TEXT NOT NULL,
    order_status ENUM('processing', 'shipped', 'delivered') NOT NULL 
),

CREATE TABLE order_items (
    order_item_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    order_id varchar(30) NOT NULL,
    product_id varchar(30) NOT NULL,
    quantity INT(11) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
)