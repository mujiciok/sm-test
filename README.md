## About

Technical assignment that demonstrates API integration and data processing. It involves creating API endpoints that retrieves search volume and Google SERP position data for a given list of keywords using the DataForSEO API. The retrieved data is used to calculate visibility metrics, which are then analyzed to generate actionable insights using OpenAI's API.

## Project initialization

### Clone project
```bash
git clone https://github.com/mujiciok/sm-test.git
cd sm-test
```

### Set .env keys
```bash
cp .env.example .env
```
```dotenv
DATA_FOR_SEO_API_HOST=
DATA_FOR_SEO_API_LOGIN=
DATA_FOR_SEO_API_PASSWORD=

OPENAI_API_KEY=
```

### Composer install
```bash
composer install
```

### Start Sail detached containers
```bash
sail up -d
```

### Stop Sail detached containers
```bash
sail down
```

## Available endpoints

- [**Get Keyword Visibility**](http://localhost/api/keyword-visibility)

    Endpoint that retrieves search volume and Google SERP data on a predefined list of keywords. Currently, it uses fixtures data, to shorten the request duration.
    Also, available in POST method, with possibility to send a custom list of keywords.
    Saves result into DB, if not already existing (unique by list of keywords).

- [**Get Keyword Insight**](http://localhost/api/keyword-insight)

    Endpoint that gets OpenAI generated insights, based on the data from the latest result of previous endpoint.
    Also, available in POST method, with possibility get insights for a different set of keywords (different DB object).

## TODOs

- Change Live endpoints usage from DataForSeo API to POST Task + GET Task endpoint
- Remove fixtures usage
- Move data retrieval into async processes (jobs)
- Change endpoints to async mode: run jobs, show progress, return data
- Handle errors related to external APIs (currently, mostly only "success" case handled)
- Add full validation for the requests to DataForSeo API and additional customization of requests
- Fix existing @TODO comments
- Tests
