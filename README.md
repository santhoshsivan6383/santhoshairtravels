# Santhosh Air Travels - Flight Ticket Management System

A complete flight ticket management system built with PHP, MySQL, and Bootstrap 5. Easy to setup on XAMPP!

## ✨ Features

- **User Authentication**: Secure login/logout system with password hashing
- **Flight Ticket Management**: Create, view, edit flight ticket bookings
- **Professional PDF Generation**: Generate printable flight tickets with client details
- **Dashboard**: Overview of all tickets with statistics
- **Bill Management**: Track ticket pricing and revenue
- **Responsive Design**: Works perfectly on desktop and mobile devices
- **Modern UI**: Built with Bootstrap 5 and custom styling

## 📋 System Requirements

- XAMPP (PHP 7.0+, MySQL 5.1+)
- Windows, Mac, or Linux
- Modern web browser

## 🚀 Installation & Setup

### Step 1: Extract Project Files
Extract the project to your XAMPP htdocs directory:
```
C:\xampp\htdocs\santhosh-airtravels\
```

### Step 2: Start XAMPP Services
1. Open XAMPP Control Panel
2. Start **Apache** and **MySQL**

### Step 3: Create Database
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create a new database (if needed)
3. Import the SQL file:
   - Open `db_setup.sql` from the project folder
   - Go to phpMyAdmin → Import
   - Select `db_setup.sql` and import
   
   OR use MySQL command line:
   ```bash
   cd C:\xampp\htdocs\santhosh-airtravels
   mysql -u root < db_setup.sql
   ```

### Step 4: Update Database Connection (if needed)
Edit `config.php` if you have custom database settings:
```php
define('DB_HOST', 'localhost');  // Your host
define('DB_USER', 'root');        // Your username
define('DB_PASS', '');            // Your password
define('DB_NAME', 'santhosh_travels'); // Your database name
```

### Step 5: Access the Application
Open your browser and go to:
```
http://localhost/santhosh-airtravels/
```

## 🔐 Default Login Credentials

- **Username**: `admin`
- **Password**: `admin123`

⚠️ **Important**: Change these credentials in production!

## 📝 Project Structure

```
santhosh-airtravels/
├── index.php              # Entry point (redirects to login/dashboard)
├── config.php             # Database configuration
├── login.php              # Login page
├── logout.php             # Logout handler
├── dashboard.php          # Main dashboard with ticket list
├── add_ticket.php         # Create new ticket form
├── view_ticket.php        # View ticket details
├── edit_ticket.php        # Edit existing ticket
├── generate_pdf.php       # Generate PDF ticket
├── db_setup.sql           # Database schema & sample data
├── fpdf/                  # PDF library (for future use)
└── README.md              # This file
```

## 🎯 How to Use

### 1. **Login**
   - Open http://localhost/santhosh-airtravels/
   - Enter demo credentials (admin/admin123)

### 2. **Create a New Ticket**
   - Click "Add New Ticket" button
   - Fill in passenger and flight details
   - Click "Create Ticket"

### 3. **View Ticket Details**
   - Click the "View" button next to any ticket in the list
   - See all details including passenger info, flight details, and pricing

### 4. **Edit Ticket**
   - Click "Edit" on ticket view page
   - Modify any details and save

### 5. **Generate PDF Ticket**
   - Click "PDF" button next to any ticket
   - A professional ticket will display
   - Click "Print or Save as PDF" to save locally

### 6. **View Dashboard Statistics**
   - Total tickets count
   - Confirmed bookings
   - Total revenue
   - Quick links to features

## 💾 Database Schema

### `users` Table
- `id`: User ID (Primary Key)
- `username`: Unique username
- `password`: Hashed password
- `email`: User email
- `created_at`: Account creation timestamp

### `tickets` Table
- `id`: Ticket ID (Primary Key)
- `user_id`: Associated user (Foreign Key)
- `client_name`: Passenger name
- `client_email`: Passenger email
- `client_phone`: Passenger phone
- `departure_city`: From city
- `arrival_city`: To city
- `departure_date`: Travel date
- `departure_time`: Departure time
- `arrival_time`: Arrival time
- `airline_name`: Airline name
- `flight_number`: Flight number
- `passenger_count`: Number of passengers
- `ticket_price`: Price per passenger
- `total_price`: Total amount
- `booking_reference`: Unique reference (SA + date + random)
- `status`: pending/confirmed/cancelled
- `notes`: Additional notes
- `created_at`: Record creation time
- `updated_at`: Last update time

## 🛡️ Security Features

- **Password Hashing**: bcrypt-based password storage
- **Session Management**: Secure session handling
- **SQL Injection Prevention**: Prepared statements
- **XSS Protection**: HTML sanitization
- **Access Control**: Login required for all pages

## 🎨 Customization

### Change Color Scheme
Edit the gradient colors in CSS (default: purple/blue gradient):
```css
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

### Add More Ticket Statuses
Edit `add_ticket.php` and `edit_ticket.php`:
```php
<select class="form-select" name="status">
    <option value="pending">Pending</option>
    <option value="confirmed">Confirmed</option>
    <option value="cancelled">Cancelled</option>
    <!-- Add more statuses here -->
</select>
```

### Customize Company Details
Edit company name and email in PHP files and update database sample data.

## 📱 Responsive Design

The application is fully responsive and works on:
- Desktop browsers (Chrome, Firefox, Safari, Edge)
- Tablets (iPad, Android tablets)
- Mobile phones (iOS, Android)

## ⏱️ Performance Tips

- Tickets are indexed by `user_id` and `created_at` for fast queries
- Dashboard loads ticket list from database efficiently
- PDF generation is done server-side for reliability

## 🐛 Troubleshooting

### Issue: "Connection failed" error
**Solution**: 
- Make sure MySQL is running in XAMPP Control Panel
- Check database credentials in `config.php`

### Issue: "404 Not Found"
**Solution**:
- Check project is in correct folder: `C:\xampp\htdocs\santhosh-airtravels\`
- Restart Apache in XAMPP

### Issue: Login not working
**Solution**:
- Check database was imported successfully
- Verify default credentials: `admin` / `admin123`

### Issue: PDF not generating
**Solution**:
- This feature uses browser printing, so any print-to-PDF driver works
- Click "Print or Save as PDF" button on the ticket page

## 📞 Support

For issues or questions, create a new ticket in the system and debug using:
- Check error logs in `C:\xampp\php\php.log`
- Use browser developer tools (F12)
- Check XAMPP MySQL logs

## 📄 License

This project is provided as-is for educational purposes.

## ✅ Checklist for First Run

- [ ] XAMPP started (Apache + MySQL)
- [ ] Database created and imported
- [ ] Can access http://localhost/santhosh-airtravels/
- [ ] Can login with admin/admin123
- [ ] Can create a new ticket
- [ ] Can view ticket details
- [ ] Can generate PDF ticket
- [ ] Can edit a ticket
- [ ] Can logout and login again

---

**Enjoy managing your travel tickets with Santhosh Air Travels!** ✈️🌍
