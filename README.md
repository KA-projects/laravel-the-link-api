## Build Setup

```bash
# Install Composer Dependencies
composer install

# Create Environment File
cp .env.example .env

# Generate Application Key
php artisan key:generate

# Run Migrations
php artisan migrate

# To generate a superuser
php artisan db:seed --class=SuperUserSeeder

# Serve the Application at http://127.0.0.1:8000/
php artisan serve
```

## Api Documentation

**First** need to get `Superuser's API token` to create the users with limited rights. Superuser's API token is placed at 'super_users' table, in `api_token` column.To get this, in terminal to enter `php artisan tinker` and next to enter `App\Models\SuperUser::all();`

### 1. Create User

Creates a new user and generates an API key for the user(`The generated API key must be saved in notepad`).

-   **URL:** `/api/create-user`
-   **Method:** `POST`
-   **Request Headers:**
    -   `Authorization`: Superuser's API token (Bearer token)
-   **Request Body:**

    ```json
    {
        "email": "user@example.com",
        "name": "John Doe",
        "password": "password of the user"
    }
    ```

    **Response Body:**

    ```json
    {
        "api_key": "generated_api_key"
    }
    ```

### 2. Create the Link

Creates a new link for a user.

-   **URL:** `/api/create-link`
-   **Method:** `POST`
-   **Request Headers:**
    -   `Authorization`: User's API key (Bearer token)
-   **Request Body:**

    ```json
    {
        "email": "user@example.com",
        "password": "password of the user",
        "link": "http://example.com",
        "public": true,
        "short_token": "custom_token(optional)"
    }
    ```

    **Response Body:**

    ```json
    {
        "response": "created_link_info"
    }
    ```

### 3.Get Links

Retrieves links associated with a user.

-   **URL:** `/api/get-links`
-   **Method:** `GET`
-   **Request Headers:**
    -   `Authorization`: User's API key (Bearer token)
-   **Query Parameters:**
    -   `email`: (optional) User's email
    -   `token`: (optional) Short token of the link
-   **Response:**

    -   If neither `email` nor `token` is provided,then returns public the links:
        ```json
        {
            "links": [
                {
                    "link_info_1"
                },
                {
                    "link_info_2"
                },
                ...
            ]
        }
        ```
    -   If only `email` is provided:
        ```json
        {
            "user-links": [
                {
                    "link_info_1"
                },
                {
                    "link_info_2"
                },
                ...
            ]
        }
        ```
    -   If both `email` and `token` are provided:
        -   If the link is found for the provided token:
            ```json
            {
                "user-link": {
                    "link_info"
                }
            }
            ```
        -   If no link is found for the provided token:
            ```json
            {
                "error": "The link was not found for the passed token"
            }
            ```
    -   If the user is not found:

        ```json
        {
            "error": "The user not found"
        }
        ```

### 4. Redirect To Link

Creates a new link for a user.

-   **URL:** `/api/{user}/{short_token}`
-   **Method:** `GET`
-   **Path Parametrs:**

    -   `user`: User's email
    -   `short_token`: Short token of the link

    **Response**

    -   Redirects to the destination URL of the link if it's public.
