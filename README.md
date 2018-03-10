# coal_chart
Webapp creating chart from coal consumption over the heating season - part of home automation.

## todo

- swap to sqlite
- add stock

## sqlite database "data/entries.db"

- table 'entries'
    - id (int) - primary key
    - amount (float) - amount of coal put to stove [kg]
    - timestamp (datetime) - timestamp of action

- table 'stock'
    - id (int) - primary key
    - amount (int) - amount of coal put to stock [kg]
    - timestamp (datetime) - timestamp of action
    - price (float) - price of order [kč]
    - bill (string) - filename of the bill scan