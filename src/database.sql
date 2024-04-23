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
    role TEXT NOT NULL CHECK(role IN ('Buyer', 'Seller', 'Admin')),
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

-- Insert sample data into books table
INSERT INTO books (title, author, language, isbn, genre_id, seller_id, condition, listed_price, description, image_url, listing_date, age_group)
VALUES 
    ('The Hobbit', 'J.R.R. Tolkien', 'English', '9780547928227', 2, 2, 'New', 22.50, 'A fantasy novel about Bilbo Baggins''s quest to reclaim the Lonely Mountain and its treasure from the dragon Smaug.', 'bookcover4.jpg', '2024-04-18', 'Children'),
    ('To Kill a Mockingbird', 'Harper Lee', 'English', '9780061120084', 3, 3, 'Used', 15.00, 'A novel set in the American South during the 1930s, addressing issues of racial injustice and morality.', 'bookcover5.jpg', '2024-04-19', 'Adults'),
    ('The Catcher in the Rye', 'J.D. Salinger', 'English', '9780316769488', 3, 1, 'Like New', 18.00, 'A coming-of-age novel narrated by a teenager named Holden Caulfield, who describes his experiences in New York City after being expelled from prep school.', 'bookcover6.jpg', '2024-04-20', 'Teens'),
    ('1984', 'George Orwell', 'English', '9780451524935', 1, 2, 'New', 16.50, '1984 is a dystopian novel by George Orwell published in 1949. The story takes place in an imagined future, the year 1984, when much of the world has fallen victim to perpetual war, omnipresent government surveillance, historical negationism, and propaganda.', 'bookcover.jpg', '2024-04-18', 'Adults'),
    ('Harry Potter and the Philosopher''s Stone', 'J.K. Rowling', 'English', '9780590353427', 2, 3, 'Used', 12.00, 'Harry Potter and the Philosopher''s Stone is the first novel in the Harry Potter series written by J.K. Rowling. It follows Harry Potter, a young wizard who discovers his magical heritage on his eleventh birthday when he receives a letter of acceptance to the Hogwarts School of Witchcraft and Wizardry.', 'bookcover2.jpeg', '2024-04-19', 'Children'),
    ('The Da Vinci Code', 'Dan Brown', 'English', '9780307474278', 3, 1, 'Like New', 20.00, 'The Da Vinci Code is a 2003 mystery thriller novel by Dan Brown. It follows symbologist Robert Langdon and cryptologist Sophie Neveu after a murder in the Louvre Museum in Paris, when they become involved in a battle between the Priory of Sion and Opus Dei over the possibility of Jesus Christ having been married to Mary Magdalene.', 'bookcover3.jpg', '2024-04-20', 'Adults');

-- Insert sample data into transactions table
INSERT INTO transactions (buyer_id, seller_id, book_id, sale_price, transaction_date, status) 
VALUES 
    (1, 2, 1, 15.00, '2024-04-19', 'Completed'),
    (2, 3, 2, 10.00, '2024-04-20', 'Completed'),
    (3, 1, 3, 18.00, '2024-04-21', 'Pending');

-- Insert sample data into messages table
INSERT INTO messages (sender_id, receiver_id, book_id, content, timestamp) 
VALUES 
    (1, 2, 1, 'Hi, I''m interested in purchasing your book.', '2024-04-18 10:00:00'),
    (2, 3, 2, 'Is this book still available?', '2024-04-19 11:00:00'),
    (3, 1, 3, 'Could you provide more details about the condition?', '2024-04-20 12:00:00');

-- Insert sample data into reviews table
INSERT INTO reviews (book_id, reviewer_id, rating, comment, date) 
VALUES 
    (1, 2, 4, 'Great book, highly recommend it!', '2024-04-20'),
    (2, 3, 5, 'Excellent read, loved every page!', '2024-04-21'),
    (3, 1, 3, 'Interesting plot but pacing was a bit slow.', '2024-04-22');

-- Insert sample data into wishlists table
INSERT INTO wishlists (user_id, book_id) 
VALUES 
    (1, 2),
    (2, 3),
    (3, 1);

-- Insert sample data into shopping_cart table
INSERT INTO shopping_cart (user_id, book_id, quantity) 
VALUES 
    (1, 3, 1),
    (2, 1, 2),
    (3, 2, 1);
