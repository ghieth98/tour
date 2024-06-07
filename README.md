Tour
"Tour" is a web application designed to provide comprehensive information about tour locations in Saudi Arabia. It aims to enhance the tourist experience by offering personalized tour recommendations based on a test taken by the user. The platform features user authentication, role-based access control, email notifications, and file uploads to ensure seamless interactions for administrators, supervisors, and tourists alike.

Table of Contents
Installation
Usage
Features
Project Structure
Contributing
License
Contact
Installation
To get a local copy up and running follow these simple steps:

Clone the repo
sh
Copy code
git clone https://github.com/ghieth98/tour.git
Features
User authentication (login, signup, logout)
Role-based access control (administrator, supervisor, tourist)
Email notifications using PHPMailer
File uploads
Personalized tour recommendations based on user test results
Project Structure
administrator/ - Contains files related to admin functionality
assets/ - Static assets like images and stylesheets
supervisor/ - Contains files related to supervisor functionality
tourist/ - Contains files related to tourist functionality
uploads/ - Directory for uploaded files
connection.php - Database connection script
cron_job.php - Script for scheduled tasks
login.php, logout.php, signup.php, validate.php - Authentication scripts


Fork the Project
Create your Feature Branch (git checkout -b feature/AmazingFeature)
Commit your Changes (git commit -m 'Add some AmazingFeature')
Push to the Branch (git push origin feature/AmazingFeature)
Open a Pull Request
License
Distributed under the MIT License. See LICENSE for more information.
