# MiniBlog - PHP & MySQL Blog Platform

A clean, responsive, and fully functional **MiniBlog application** built with **PHP**, **MySQL**, and **Bootstrap 5**. This project allows users to read, search, comment, and like blog posts. Admins can manage posts, categories, tags, and users through a dedicated **Admin Panel**.

---

## Table of Contents

- [Features](#features)
- [Technologies Used](#technologies-used)
- [Project Structure](#project-structure)
- [Setup Instructions](#setup-instructions)
- [Database Schema](#database-schema)
- [Functionalities](#functionalities)
- [Admin Panel](#admin-panel)
- [Screenshots](#screenshots)
- [License](#license)

---

## Features

### User-Side Features

- View all posts in a clean card layout.
- Search posts by title or content.
- Post details page with:
  - Thumbnail image
  - Content
  - Category
  - Views count
  - Likes
  - Comments
- Add comments (requires login)
- Like/unlike posts (requires login)
- Pagination for better navigation.
- Responsive design using Bootstrap.

### Admin-Side Features

- Admin authentication and access control.
- Full CRUD operations for:
  - Posts
  - Categories
  - Tags
  - Users
  - Comments
- Post management:
  - Add/edit/delete posts
  - Assign multiple tags
  - Upload thumbnail images
- Category and tag management.
- Comment moderation.
- All forms include validation and proper error handling.

---

## Technologies Used

- **Backend:** PHP (plain)
- **Frontend:** HTML5, CSS3, Bootstrap 5
- **Database:** MySQL / MariaDB
- **JavaScript:** jQuery for AJAX (comments & likes)
- **Rich Text Editor:** CKEditor 5
- **Session Management:** PHP sessions for authentication
- **Security Features:**
  - Input sanitization
  - Prepared statements (PDO)
  - Admin-only access for restricted pages

---

## Project Structure

MiniBlog/
│
├── admin/
│ ├── dashboard.php
│ ├── posts.php
│ ├── post_add.php
│ ├── post_edit.php
│ ├── post_delete.php
│ ├── categories.php
│ ├── category_add.php
│ ├── category_edit.php
│ ├── category_delete.php
│ ├── tags.php
│ ├── tag_add.php
│ ├── tag_edit.php
│ ├── tag_delete.php
│ └── users.php
│
├── auth/
│ ├── login.php
│ └── logout.php
    └── register.php

│
├── uploads/ # Stores all uploaded images (thumbnails)
├── assets/
│ ├── css/
│ │ └── custom.css
│ └── js/
│ ├── main.js
│ 
├── includes/
│ ├── config.php # Database connection & site config
│ ├── functions.php # Reusable functions (auth, CRUD, helpers)
│ ├── header.php
│ └── footer.php
├── index.php # Home page with all posts
├── post.php # Single post details page
├── like.php # AJAX handler for likes
├── comment.php # AJAX handler for comments
└── README.md