from fastapi import FastAPI,Form
import mysql.connector
from fastapi.middleware.cors import CORSMiddleware


app=FastAPI()

conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="Jack2003@",
    database="mydb"
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=["*"],
    allow_methods=["*"],
    allow_headers=["*"]
)

@app.get("/get_dish_ranking")
def get_dish_ranking(limit:int=10):
    cursor=conn.cursor(dictionary=True)
    cursor.execute("""
        SELECT nome_piatto, AVG(valutazione) as valutazione_media
        FROM piatti
        GROUP BY nome_piatto
        ORDER BY valutazione_media DESC
        LIMIT %s
                   """,(limit,))

    records=cursor.fetchall()
    return records
