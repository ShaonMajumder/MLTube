# Natok - OTT Platform

## Overview

**Natok** is an Over-The-Top (OTT) platform designed to provide seamless streaming experiences. The platform integrates a variety of features including streaming, object detection, push notifications, and more.

Security is HIGH - as systems have access over only the files which is really necessary.
EASY to Deploy - All secnarios are revised.
EASY to Develop - as directories are mounted with the docker container, rest of the sources is owned by host syestems.

## Architecture

The Natok platform is built using a **Microservice** architecture, allowing different components to function independently and communicate with each other via APIs.

## Languages

- **Python**: Utilized for backend services, including object detection and machine learning components.
- **PHP**: Used for web application development and integration with backend services.
- **JavaScript**: Employed for client-side scripting and enhancing user interactivity.
- **Vue.js**: Used as the front-end framework to build responsive and dynamic user interfaces.

## Scaling

The platform employs **Containerization and Virtualization** techniques to ensure scalability and efficient resource utilization.

## Technology Stack

- **Docker**: (**Virtualization**) Used for containerizing applications, ensuring consistency across different environments.
- **RabbitMQ**: (**Queue Management**) Employed as a message broker to handle asynchronous tasks and inter-service communication.

## Techniques

- **Push Notification**: Integrated to provide real-time notifications to users.
- **OTP (One-Time Password)**: Used for secure user authentication and verification.
- **Streaming**: Core feature allowing users to stream content seamlessly.
- **Machine Learning - Object Detection**: Utilized for advanced features like automatic tagging and content analysis.

## Design Patterns

- **Singleton Pattern**: Ensures that a class has only one instance and provides a global point of access to it.
- **Factory Pattern**: Used for creating objects without specifying the exact class of the object that will be created.
- **Observer Pattern**: Implements a subscription mechanism to allow multiple objects to listen and react to events.
- **Strategy Pattern**: Defines a family of algorithms and makes them interchangeable.
- **Decorator Pattern**: Adds additional responsibilities to objects dynamically.

## Frontend Development

- **JavaScript**: Provides interactive features and dynamic content updates on the client-side.
- **Vue.js**: Used for building the user interface, handling state management, and creating reactive components.

## Setup and Installation

1. **Clone the Repository**

   ```bash
   git clone https://github.com/your-repository/natok-ott-platform.git
   cd natok-ott-platform