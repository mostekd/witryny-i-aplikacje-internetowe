def znajdz_najwieksza_liczbe():
    a = int(input("Podaj liczbę a: "))
    b = int(input("Podaj liczbę b: "))
    c = int(input("Podaj liczbę c: "))

    if a > b:
        if a > c:
            max_num = a
        else:
            max_num = c
    else:
        if b > c:
            max_num = b
        else:
            max_num = c

    print("Największa liczba to:", max_num)

znajdz_najwieksza_liczbe()
