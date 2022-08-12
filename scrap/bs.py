from random import choices, randint
from numpy import number
import requests
from bs4 import BeautifulSoup
import uuid
from threading import Thread
import json

MASTER_URL = "https://www.goodreads.com"

GENRE_LIST = [
    "Literary Fiction",
    "Mystery",
    "Thriller",
    "Horror",
    "Historical",
    "Romance",
    "Western",
    "Comedy",
    "Science Fiction",
    "Fantasy",
    "Dystopian",
    "Magical ",
    "Literature",
]

url = "https://www.goodreads.com/list/show/264.Books_That_Everyone_Should_Read_At_Least_Once"
resp = requests.get(url)
page = BeautifulSoup(resp.text, "html.parser")

pagination = int(page.find("div", {"class" : "pagination"}).find_all("a")[-2].decode_contents())

result_file = open("result.txt", "w")

def scrap(book_url):
    global result_file, GENRE_LIST
    try:
        resp = requests.get(book_url)
        book_soup = BeautifulSoup(resp.text, "html.parser")
        title = book_soup.find("h1", {"id" : "bookTitle"}).decode_contents().strip().replace("'", "\'\'")
        author = book_soup.find("span", {"itemprop" : "name"}).decode_contents().replace("'", "\'\'")
        img_url = book_soup.find("img", {"id" : "coverImage"})['src'].replace("'", "\'\'")
        desc = book_soup.find("div", {"id" : "description"}).find_all("span")[1].getText().replace("'", "\'\'")
        publishing_info = book_soup.find("div", {"id" : "details"}).find_all("div", {"class" : "row"})[1].getText().strip().replace("'", "\'\'")
        language = book_soup.find("div", {"class" : "infoBoxRowItem", "itemprop" : "inLanguage"}).decode_contents()
        publishing_info = " ".join((" ".join("" if x == "" else x.strip() for x in publishing_info.split(" "))).split()).split(" by ")
        publisher = publishing_info[1].split("(")[0].replace("'", "\'\'")
        date = publishing_info[0][10:]
        genre = "".join(choices(GENRE_LIST))
        price = randint(10, 85)
        data = "INSERT INTO books VALUES ('{}', '{}', '{}', '{}', '{}', '{}', '{}', '{}', '{}', '{}')".format(uuid.uuid4(), title, author, language, img_url, genre, price, date, publisher, desc)
        result_file.write(
            data + "\n"
        )
    except Exception:
        return

for i in range(pagination):
    numbered_url = url + "?page={}".format(i)
    resp = requests.get(numbered_url)
    all_books = BeautifulSoup(resp.text, "html.parser").find_all("a", {"class" : "bookTitle"})
    for book_link in all_books:
        try:
            print(book_link.getText().strip())
            book_url = MASTER_URL + book_link['href']
            t = Thread(target=scrap, args=(book_url,))
            t.start()
        except Exception as e:
            continue
result_file.close()