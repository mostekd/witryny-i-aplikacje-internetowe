def algorytm():
    n = 5
    d = [3, 7, 8, 2, 1]
    j = n - 1
    
    while j >= 1:
        i = 0
        while i <= j-1:
            if d[i] > d[i + 1]:
                tmp = d[i]
                d[i] = d[i + 1]
                d[i + 1] = tmp
            else:
                i += 1
        j -= 1
    return d

sorted_list = algorytm()
print(sorted_list)
    