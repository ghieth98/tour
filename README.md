# Tour

"Tour" is a web application designed to provide comprehensive information about tour locations in Saudi Arabia. It aims to enhance the tourist experience by offering personalized tour recommendations based on a test taken by the user. The platform features user authentication, role-based access control, email notifications, and file uploads to ensure seamless interactions for administrators, supervisors, and tourists alike.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Features](#features)
- [Project Structure](#project-structure)
- [Contributing](#contributing)
- [License](#license)
- [Contact](#contact)

## Installation

To get a local copy up and running, follow these simple steps:

1. Clone the repo
   ```sh
   git clone https://github.com/ghieth98/tour.git
   ```

## Usage

To use the application, navigate to the respective roles (administrator, supervisor, or tourist) and perform actions according to your role privileges.

## Features

- User authentication (login, signup, logout)
- Role-based access control (administrator, supervisor, tourist)
- Email notifications using PHPMailer
- File uploads
- Personalized tour recommendations based on user test results

## Project Structure

```plaintext
administrator/  - Contains files related to admin functionality
assets/         - Static assets like images and stylesheets
supervisor/     - Contains files related to supervisor functionality
tourist/        - Contains files related to tourist functionality
uploads/        - Directory for uploaded files
connection.php  - Database connection script
cron_job.php    - Script for scheduled tasks
login.php       - Authentication script for logging in users
logout.php      - Authentication script for logging out users
signup.php      - Authentication script for signing up users
validate.php    - Script for validating user input
```

## Contributing

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

Distributed under the MIT License. See `LICENSE` for more information.

