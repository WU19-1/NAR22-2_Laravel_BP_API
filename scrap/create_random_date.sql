UPDATE books SET publication_date = CURRENT_DATE - INTERVAL FLOOR(RAND() * 36500) DAY;
