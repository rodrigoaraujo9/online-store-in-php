-- Drop existing tables if they exist
DROP TABLE IF EXISTS shopping_cart;
DROP TABLE IF EXISTS wishlists;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS genres;
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
    user_id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    role TEXT NOT NULL CHECK(role IN ('User', 'Admin')),
    profile_picture_url TEXT,
    registered_date DATE NOT NULL,
    bio TEXT
);

-- Create genres table
CREATE TABLE genres (
    genre_id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT
);

-- Create books table
CREATE TABLE books (
    book_id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    author TEXT NOT NULL,
    language TEXT NOT NULL,
    isbn TEXT NOT NULL,
    genre_id INTEGER NOT NULL,
    seller_id INTEGER NOT NULL,
    condition TEXT NOT NULL,
    listed_price REAL NOT NULL,
    description TEXT,
    image_url TEXT,
    listing_date DATE NOT NULL,
    age_group TEXT NOT NULL CHECK(age_group IN ('Children', 'Teens', 'Adults')),
    FOREIGN KEY (genre_id) REFERENCES genres(genre_id),
    FOREIGN KEY (seller_id) REFERENCES users(user_id)
);

-- Create transactions table
CREATE TABLE transactions (
    transaction_id INTEGER PRIMARY KEY AUTOINCREMENT,
    buyer_id INTEGER NOT NULL,
    seller_id INTEGER NOT NULL,
    book_id INTEGER NOT NULL,
    sale_price REAL NOT NULL,
    transaction_date DATE NOT NULL,
    status TEXT NOT NULL CHECK(status IN ('Pending', 'Completed', 'Cancelled')),
    FOREIGN KEY (buyer_id) REFERENCES users(user_id),
    FOREIGN KEY (seller_id) REFERENCES users(user_id),
    FOREIGN KEY (book_id) REFERENCES books(book_id)
);

-- Create messages table
CREATE TABLE messages (
    message_id INTEGER PRIMARY KEY AUTOINCREMENT,
    sender_id INTEGER NOT NULL,
    receiver_id INTEGER NOT NULL,
    book_id INTEGER,
    content TEXT NOT NULL,
    timestamp DATETIME NOT NULL,
    FOREIGN KEY (sender_id) REFERENCES users(user_id),
    FOREIGN KEY (receiver_id) REFERENCES users(user_id),
    FOREIGN KEY (book_id) REFERENCES books(book_id)
);

-- Create reviews table
CREATE TABLE reviews (
    review_id INTEGER PRIMARY KEY AUTOINCREMENT,
    book_id INTEGER NOT NULL,
    reviewer_id INTEGER NOT NULL,
    rating INTEGER NOT NULL CHECK(rating BETWEEN 1 AND 5),
    comment TEXT,
    date DATE NOT NULL,
    FOREIGN KEY (book_id) REFERENCES books(book_id),
    FOREIGN KEY (reviewer_id) REFERENCES users(user_id)
);

-- Create wishlists table
CREATE TABLE wishlists (
    wishlist_id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    book_id INTEGER NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (book_id) REFERENCES books(book_id)
);

-- Create shopping_cart table
CREATE TABLE shopping_cart (
    cart_id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    book_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL CHECK(quantity > 0),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (book_id) REFERENCES books(book_id)
);

-- Insert sample data into genres table
INSERT INTO genres (name, description) 
VALUES 
    ('Science Fiction', 'Fictional stories based on scientific discoveries, space exploration, etc.'),
    ('Fantasy', 'Stories often set in imaginary worlds with magical elements.'),
    ('Mystery', 'Intriguing narratives focused on solving puzzles or uncovering secrets.');
