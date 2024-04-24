## Api Documentation

### 1. Create User

Creates a new user and generates an API key for the user.

-   **URL:** `/api/create-user`
-   **Method:** `POST`
-   **Request Headers:**
    -   `Authorization`: Superuser's API token (Bearer token)
-   **Request Body:**

    ````json
    {
        "email": "user@example.com",
        "name": "John Doe",
        "password": "password"
    }
    **Response Body:**
    ```json
    {
        "api_key": "generated_api_key"
    }


    ````
