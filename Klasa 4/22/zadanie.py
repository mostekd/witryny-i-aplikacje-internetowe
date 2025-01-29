def odejmij_wieksza_od_mniejsza():
    a = int(input("Podaj liczbę a: "))
    b = int(input("Podaj liczbę b: "))

    while(a!=b):
        if(a>b):
            a = a-b
        else:
            b = b-a
            
    print(a)
odejmij_wieksza_od_mniejsza()