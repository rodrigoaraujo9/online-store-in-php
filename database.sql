-- Create the 'users' table
CREATE TABLE users (
    user_id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL, -- Store hashed passwords
    email TEXT UNIQUE NOT NULL,
    role TEXT NOT NULL CHECK(role IN ('Buyer', 'Seller', 'Admin')), -- Ensure role is one of the specified values
    profile_picture_url TEXT,
    registered_date DATE NOT NULL
);

-- Create the 'genres' table
CREATE TABLE genres (
    genre_id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT
);

-- Create the 'books' table
CREATE TABLE books (
    book_id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    author TEXT NOT NULL,
    isbn TEXT NOT NULL,
    genre_id INTEGER,
    seller_id INTEGER NOT NULL,
    condition TEXT NOT NULL,
    listed_price REAL NOT NULL,
    description TEXT,
    image_url TEXT,
    listing_date DATE NOT NULL,
    FOREIGN KEY (genre_id) REFERENCES genres(genre_id),
    FOREIGN KEY (seller_id) REFERENCES users(user_id)
);

-- Create the 'transactions' table
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

-- Create the 'messages' table
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

-- Create the 'reviews' table
CREATE TABLE reviews (
    review_id INTEGER PRIMARY KEY AUTOINCREMENT,
    book_id INTEGER NOT NULL,
    reviewer_id INTEGER NOT NULL,
    rating INTEGER NOT NULL CHECK(rating BETWEEN 1 AND 5), -- Ratings between 1 and 5
    comment TEXT,
    date DATE NOT NULL,
    FOREIGN KEY (book_id) REFERENCES books(book_id),
    FOREIGN KEY (reviewer_id) REFERENCES users(user_id)
);

-- Create the 'wishlists' table
CREATE TABLE wishlists (
    wishlist_id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    book_id INTEGER NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (book_id) REFERENCES books(book_id)
);

-- Create the 'shopping_cart' table
CREATE TABLE shopping_cart (
    cart_id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    book_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL CHECK(quantity > 0),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (book_id) REFERENCES books(book_id)
);
