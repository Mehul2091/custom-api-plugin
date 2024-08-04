# Custom Rest API For Wordpress

# Installation

Upload the plugin files to the `/wp-content/plugins/ directory, or install the plugin through the WordPress plugins screen directly.

Activate `Custom Api Plugin` plugin through the ‘Plugins’ screen in WordPress

# Dependancy

Requred Plugin:
[JWT Auth – WordPress JSON Web Token Authentication](https://wordpress.org/plugins/jwt-auth/)

### Integration Steps:
    
    
    Most shared hosts have disabled the HTTP Authorization Header by default.

    To enable this option you’ll need to edit your .htaccess file by adding the following:

    RewriteEngine on
    RewriteCond %{HTTP:Authorization} ^(.*)
    RewriteRule ^(.*) - [E=HTTP_AUTHORIZATION:%1]

    To add the secret key, edit your wp-config.php file and add a new constant called JWT_AUTH_SECRET_KEY and JWT_AUTH_CORS_ENABLE

    define('JWT_AUTH_SECRET_KEY', 'OE0DHgPuCfmAQidIFDP6PIio3ctdERSD');
    define('JWT_AUTH_CORS_ENABLE', true);



# Api list :

* Login
* Submit data
* Get data


## Login Endpoint

### URL
`http://localhost/wordpress/wp-json/custom-auth/v1/login`

### Method
`POST`

### Description
This endpoint allows users to log in by providing their username and password. Upon successful authentication, the endpoint returns an authentication token and user information.

### Request

- **Content-Type**: `multipart/form-data`

### Parameters

| Parameter | Type   | Required | Description       |
|-----------|--------|----------|-------------------|
| username  | string | Yes      | The user's username. |
| password  | string | Yes      | The user's password. |

### Example Request

```sh
curl --location 'http://localhost/wordpress/wp-json/custom-auth/v1/login' \
--form 'username="test"' \
--form 'password="123456"'
```

### Example Response 

```sh
{
    "success": true,
    "statusCode": 200,
    "message": "Credential is valid",
    "data": {
        "token": "auth_token",
        "id": 1,
        "email": "test@gmail.com",
        "nicename": "test",
        "firstName": "test",
        "lastName": "dev",
        "displayName": "test dev"
    }
}
```


## Submit Data Endpoint

### URL
`http://localhost/wordpress/wp-json/custom-auth/v1/submit_data`

### Method
`POST`

### Description
This endpoint allows authenticated users to submit personal data including their first name, last name, email, and mobile number.

### Request

- **Content-Type**: `multipart/form-data`
- **Authorization**: Bearer token required in the header

### Headers

| Header        | Type   | Required | Description               |
|---------------|--------|----------|---------------------------|
| Authorization | string | Yes      | Bearer token for authentication. |

### Parameters

| Parameter  | Type   | Required | Description            |
|------------|--------|----------|------------------------|
| first_name | string | Yes      | The user's first name. |
| last_name  | string | Yes      | The user's last name.  |
| email      | string | Yes      | The user's email.      |
| mobile     | string | Yes      | The user's mobile number. |

### Example Request

```sh
curl --location 'http://localhost/wordpress/wp-json/custom-auth/v1/submit_data' \
--header 'Authorization: Bearer your_auth_token_here' \
--form 'first_name="Test"' \
--form 'last_name="Dev"' \
--form 'email="test@mailinator.com"' \
--form 'mobile="1234567890"'

```

### Example Response 

```sh
{
    "success": true,
    "statusCode": 200,
    "message": "Data inserted successfully",
    "data": {
        "first_name": "Test",
        "last_name": "Dev",
        "email": "test@mailinator.com",
        "phone": "1234567890",
        "created_at": "2024-08-02 19:03:03",
        "id": 1
    }
}
```


## Get Data Endpoint

### URL
`http://localhost/wordpress/wp-json/custom-auth/v1/get_data`

### Method
`GET`

### Description
This endpoint allows authenticated users to retrieve personal data associated with their account.

### Request

- **Content-Type**: `application/json`
- **Authorization**: Bearer token required in the header

### Headers

| Header        | Type   | Required | Description               |
|---------------|--------|----------|---------------------------|
| Authorization | string | Yes      | Bearer token for authentication. |

### Example Request

```sh
curl --location 'http://localhost/wordpress/wp-json/custom-auth/v1/get_data' \
--header 'Authorization: Bearer your_auth_token_here'

```

### Example Response 

```sh
{
    "success": true,
    "statusCode": 200,
    "message": "Data recieved successfully",
    "data": [
        {
            "id": 1,
            "first_name": "Test",
            "last_name": "Dev",
            "email": "test@mailinator.com",
            "phone": "1234567890",
            "created_at": "2024-08-02 19:03:03"
        }
    ]
}
```
