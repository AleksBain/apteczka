import pandas as pd

# Wczytanie pliku CSV
df = pd.read_csv("/Applications/MAMP/htdocs/apteczka/scripts/Rejestr_Produktow_Leczniczych_calosciowy_stan_na_dzien_20250501.csv", sep=";", encoding="utf-8")

# Nowa lista do przechowywania przetworzonych danych
new_rows = []

# Funkcja do rozdzielania danych na poszczególne rekordy
import re

def process_opakowanie(opakowanie_info, produkt_id):
    linie = opakowanie_info.strip().split('\n')
    i = 0

    while i < len(linie) - 1:
        kod_linia = linie[i].strip()
        opis_linia = linie[i + 1].strip()

        # Sprawdź, czy linia wygląda na kod kreskowy (14 cyfr na początku)
        if re.match(r'^\d{14} ¦', kod_linia):
            parts = kod_linia.split(' ¦ ')
            if len(parts) >= 2:
                kod_kreskowy = parts[0].strip()
                symbol_dostepu = parts[1].strip()
                new_rows.append({
                    'produkt_id': produkt_id,
                    'kod_kreskowy': kod_kreskowy,
                    'symbol_dostepu': symbol_dostepu,
                    'opis': opis_linia
                })
                i += 2  # przechodzimy do następnej pary
            else:
                i += 1  # uszkodzony wpis, pomijamy
        else:
            i += 1  # nie zaczyna się od kodu kreskowego, pomijamy


# Przetwarzanie każdej pozycji w kolumnie 'Opakowanie'
for index, row in df.iterrows():
    produkt_id = row['Identyfikator Produktu Leczniczego']
    opakowanie_info = row['Opakowanie']
    
    if isinstance(opakowanie_info, str):
        process_opakowanie(opakowanie_info, produkt_id)

# Tworzymy DataFrame z przetworzonymi danymi
new_df = pd.DataFrame(new_rows)

# Konwersja kolumny produkt_id do integer
new_df['produkt_id'] = new_df['produkt_id'].astype(int)

# Ustawienie kolejności kolumn: produkt_id jako pierwsze
new_df = new_df[['produkt_id', 'kod_kreskowy', 'symbol_dostepu', 'opis']]

# Zapisz dane do nowego pliku CSV bez nagłówka
new_df.to_csv('przetworzone_dane2.csv', index=False, header=False, sep=',', encoding='utf-8')


# Podgląd przetworzonych danych
print(new_df.head())
