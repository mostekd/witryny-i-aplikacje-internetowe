def liczba_a():
    a = int(input("Podaj liczbę a: "))

    if a < 0:
            print("liczba a jest ujemna")
    else:
        if a > 0:
            print("liczba a jest dodatnia")
        else:
            print("liczba a jest równa zero")


liczba_a()