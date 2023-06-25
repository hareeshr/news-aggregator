News Aggregator
===============

A News Aggregator is a web application that allows users to gather and read articles from various sources in a clean and easy-to-read format. This project utilizes React and Typescript on the frontend and Laravel on the backend to provide a seamless user experience.

Requirements
------------

1.  User authentication and registration: Users can create an account and log in to the website to save their preferences and settings.
    
2.  Article search and filtering: Users can search for articles by keyword and filter the results by date, category, and source.
    
3.  Personalized news feed: Users can customize their news feed by selecting preferred sources, categories, and authors.
    
4.  Mobile-responsive design: The website is optimized for viewing on mobile devices.
    

Data Sources
------------

The News Aggregator retrieves articles from the following data sources:

*   [NewsAPI](https://newsapi.org/): A comprehensive news API that provides access to articles from various sources.
*   [The Guardian](https://www.theguardian.com/): A renowned news organization.
*   [New York Times](https://www.nytimes.com/): A leading newspaper publication.

How to Run
----------

To run the News Aggregator, follow these steps:

1.  Clone the repository to your local machine:

`git clone <repository-url>`

2.  Navigate to the project directory:

`cd news-aggregator`

3.  Copy the `./env/backend.env.example` file to `./env/backend.env`:

`cp ./env/backend.env.example ./env/backend.env`

4.  Fill in the required details such as database credentials and API keys in the `./env/backend.env` file.
    
5.  Build and start the Docker containers using `docker-compose`:
    

`docker-compose build`
`docker-compose up -d`

6.  Once the containers are up and running, you can access the News Aggregator website by visiting `http://localhost` in your web browser.

Additional Notes
----------------

*   Ensure that you have Docker installed and running on your system before running the application.
*   Make sure to provide valid API keys for the data sources mentioned above to retrieve articles successfully.
*   If you encounter any issues during setup or while running the application, please refer to the project's documentation or seek assistance from the project maintainers.

Contributing
------------

We welcome contributions to enhance the News Aggregator project. If you find any bugs or have suggestions for improvement, please open an issue or submit a pull request on the repository.

License
-------

This project is licensed under the [MIT License](LICENSE). Feel free to modify and distribute the code as per the terms of the license.
