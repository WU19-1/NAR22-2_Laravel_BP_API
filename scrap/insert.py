from typing import final
import mysql.connector

con = mysql.connector.connect(host="localhost", database="elisbookvendor", user="root", password="")
if con.is_connected():
    q_file = open("result.sql", "r")
    qs = q_file.readlines()
    q_file.close()
    for q in qs:
        try:
            # print(q.rstrip())
            cur = con.cursor()
            cur.execute("{}".format(q))
        except Exception:
            pass
        # finally:
        #     input()
    try:
        cur.execute("""
            UPDATE books
            SET
                publication_date = CURRENT_DATE - INTERVAL FLOOR(RAND() * 36500) DAY,
                stock = RAND() * 50 + 100;
        """)

    except Exception:
        pass
    con.commit()
    con.close()
else:
    print("Connection failed!")
