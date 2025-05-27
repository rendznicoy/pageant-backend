# Web-Based Pageant Scoring System – Backend

This is the backend API of a web-based pageant scoring and tabulation system developed for Visayas State University. It is built using Laravel and MySQL, and supports real-time score updates, user authentication, and automated report generation.

## Features

- Laravel-based RESTful API
- Real-time scoring via WebSockets with Pusher
- DOMPDF integration for PDF report generation
- Secure user authentication and role-based access (admin, tabulators, and judges)
- MySQL database with normalized schema up to 3NF
- ISO/IEC 25010-aligned evaluation readiness

## Technologies Used

- **Framework**: Laravel 10+
- **Database**: MySQL (InnoDB)
- **PDF Generation**: DOMPDF
- **Real-Time Communication**: Pusher
- **Local Development**: Laragon

## API Endpoints

The backend provides API endpoints for:
- User authentication and login
- Score submission and retrieval
- Candidate and event management
- PDF report generation

## License

None
