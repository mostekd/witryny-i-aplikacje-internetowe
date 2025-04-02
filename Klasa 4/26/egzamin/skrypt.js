// Tablica z odpowiedziami Krzysztofa
const odpowiedziKrzysztofa = [
    "Świetnie!",
    "Kto gra główną rolę?",
    "Lubisz filmy Tego reżysera?",
    "Będę 10 minut wcześniej",
    "Może kupimy sobie popcorn?",
    "Ja wolę Colę",
    "Zaproszę jeszcze Grześka",
    "Tydzień temu też byłem w kinie na Diunie",
    "Ja funduję bilety"
];

// Funkcja dodająca wiadomość Jolanty
function dodajWiadomoscJolanty() {
    const input = document.getElementById('message-input');
    const wiadomosc = input.value.trim();
    
    if (wiadomosc) {
        const chatWindow = document.getElementById('chat-window');
        
        // Tworzenie nowego bloku wiadomości
        const nowaWiadomosc = document.createElement('div');
        nowaWiadomosc.className = 'message jolanta';
        
        // Dodawanie obrazka
        const obrazek = document.createElement('img');
        obrazek.src = 'Jolka.jpg';
        obrazek.alt = 'Jolanta Nowak';
        
        // Dodawanie tekstu wiadomości
        const tekst = document.createElement('p');
        tekst.textContent = wiadomosc;
        
        // Łączenie elementów
        nowaWiadomosc.appendChild(obrazek);
        nowaWiadomosc.appendChild(tekst);
        
        // Dodawanie do okna chatu
        chatWindow.appendChild(nowaWiadomosc);
        
        // Przewijanie do nowej wiadomości
        nowaWiadomosc.scrollIntoView();
        
        // Czyszczenie pola input
        input.value = '';
    }
}

// Funkcja dodająca losową odpowiedź Krzysztofa
function dodajLosowaOdpowiedz() {
    const chatWindow = document.getElementById('chat-window');
    
    // Losowanie odpowiedzi
    const losowyIndex = Math.floor(Math.random() * odpowiedziKrzysztofa.length);
    const wiadomosc = odpowiedziKrzysztofa[losowyIndex];
    
    // Tworzenie nowego bloku wiadomości
    const nowaWiadomosc = document.createElement('div');
    nowaWiadomosc.className = 'message krzysztof';
    
    // Dodawanie obrazka
    const obrazek = document.createElement('img');
    obrazek.src = 'Krzysiek.jpg';
    obrazek.alt = 'Krzysztof Łukasiński';
    
    // Dodawanie tekstu wiadomości
    const tekst = document.createElement('p');
    tekst.textContent = wiadomosc;
    
    // Łączenie elementów
    nowaWiadomosc.appendChild(obrazek);
    nowaWiadomosc.appendChild(tekst);
    
    // Dodawanie do okna chatu
    chatWindow.appendChild(nowaWiadomosc);
    
    // Przewijanie do nowej wiadomości
    nowaWiadomosc.scrollIntoView();
}

// Dodawanie event listenerów
document.getElementById('send-button').addEventListener('click', dodajWiadomoscJolanty);
document.getElementById('random-button').addEventListener('click', dodajLosowaOdpowiedz);

// Obsługa wysyłania wiadomości przez Enter
document.getElementById('message-input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        dodajWiadomoscJolanty();
    }
});