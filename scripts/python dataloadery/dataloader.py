import pandas as pd



df = pd.read_csv("/Applications/MAMP/htdocs/apteczka/scripts/Rejestr_Produktow_Leczniczych_calosciowy_stan_na_dzien_20250501.csv", sep=";", encoding="utf-8")


produkty = df[[
    'Identyfikator Produktu Leczniczego',
    'Nazwa Produktu Leczniczego',
    'Substancja czynna',
    'Moc',
    'Postać farmaceutyczna',
    'Kod ATC',
    'Typ procedury',
    'Numer pozwolenia',
    'Ważność pozwolenia',
    'Podmiot odpowiedzialny',
    'Kraj wytwórcy',
    'Ulotka',
    'Charakterystyka',
    'Rodzaj preparatu'
]]


produkty.columns = [
    'id',
    'nazwa_handlowa',
    'substancja_czynna',
    'moc',
    'postac_farmaceutyczna',
    'kod_atc',
    'typ_procedury',
    'numer_pozwolenia',
    'waznosc_pozwolenia',
    'podmiot_odpowiedzialny',
    'kraj_produkcji',
    'url_ulotka',
    'url_chpl',
    'rodzaj_preparatu'
]


produkty.to_csv("produkty_zaktualizowane.csv", index=False, encoding="utf-8")
