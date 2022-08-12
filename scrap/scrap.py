import asyncio
import pyppeteer

async def main():
    # init browser
    browser = await pyppeteer.launch(headless=True)
    page = await browser.newPage()
    gr_list = open("lists.txt", "r").readlines()

    for gr in gr_list:
        await page.goto(gr, { 'waitUntil' : 'networkidle2' })
        await page.waitForSelector("div.pagination")
        pagination = await page.querySelector("div.pagination")
        total_page = await pagination.querySelectorAll("a")
    
asyncio.get_event_loop().run_until_complete(main())