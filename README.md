# SSO-MIKROTIK

![GitHub Repo stars](https://img.shields.io/github/stars/maumhmd-sh/SSO-MIKROTIK?style=flat-square)
![GitHub forks](https://img.shields.io/github/forks/maumhmd-sh/SSO-MIKROTIK?style=flat-square)
![GitHub issues](https://img.shields.io/github/issues/maumhmd-sh/SSO-MIKROTIK?style=flat-square)
![License](https://img.shields.io/github/license/maumhmd-sh/SSO-MIKROTIK?style=flat-square)

---

## 📄 Project Description

**SSO-MIKROTIK** is a web-based Single Sign-On (SSO) authentication system for MikroTik Hotspot networks. Built with **PHP** and **HTML**, it provides a simple and efficient login portal for users to access Wi-Fi networks.

This project is ideal for environments like campuses, schools, cafes, or public areas using MikroTik Hotspot devices.

---

## 📁 Repository Structure

SSO-MIKROTIK/
├── backend/
│ └── login.php # Handles user authentication
├── interface/
│ └── index.php # Main portal page
├── login/
│ └── login.html # User login form
└── README.md # Project documentation


---

## ⚙️ How to Use

### 1. Setup

- Ensure your web server has **PHP 7+** installed.
- Clone or download this repository to your web server directory.

```bash
git clone https://github.com/maumhmd-sh/SSO-MIKROTIK.git
```

### 2. Configure MikroTik

- Access your MikroTik device via **Winbox** or **WebFig**.
- Go to **Files** and upload the `login.html` page for the Hotspot login.
- Configure your Hotspot to redirect users to this SSO portal.

### 3. Configure the Backend

- Open `backend/login.php` and update database credentials or user settings if necessary.
- Ensure your web server can communicate with the MikroTik Hotspot for login validation.

### 4. Access Portal

- Users connecting to the Wi-Fi will be redirected to the SSO login page.
- Upon successful login, users gain access to the network.

---

## 📌 Features

- Simple SSO authentication for MikroTik Hotspot.
- Lightweight PHP + HTML implementation.
- Easy integration with existing MikroTik Hotspot setups.

---

## 🖼️ Screenshots (Optional)

![Login Page](path/to/screenshot.png)  
*Example of the login portal interface.*

---

## 📝 License

This project is licensed under the **MIT License**.
