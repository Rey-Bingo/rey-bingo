const MAX_MESSAGES = 5;
const MAX_CONFETTI = 50;
const POLL_INTERVAL = 2000;
let numbersgenerated = [];
let lastNumbers = fiveNumbers || [];
let lastMessageIntervalGet;
let userCountInterval;
let generationInterval;
let lastInterval;
let narrationAudio;
let soundWinner;
let isGameFinishedShown = false;
let messagesDisplayed = [];
let intervalNextGame;

const container = document.querySelector('.content-cartons');
const cartons = document.querySelectorAll('.bingo-carton');

if (cartons.length == 1) {
    container.classList.add('one-carton');
} else if (cartons.length == 2) {
    container.classList.add('two-cartons');
} else if (cartons.length == 3) {
    container.classList.add('three-cartons');
} else if (cartons.length == 4) {
    container.classList.add('four-cartons');
}

const $id = (id) => document.getElementById(id);

function scrollToBottom() {
    const el = $id("message-display");
    el.scrollTop = el.scrollHeight;
}

function getColumnClass(number) {
    if (number <= 15) return 'B';
    if (number <= 30) return 'I';
    if (number <= 45) return 'N';
    if (number <= 60) return 'G';
    return 'O';
}

function createMessageBubble(content, profilePicUrl) {
    const bubble = document.createElement("div");
    bubble.classList.add("message-bubble");

    const img = document.createElement("img");
    img.classList.add("profile-pic");
    img.src = profilePicUrl;

    const span = document.createElement("span");
    span.textContent = content;
    span.classList.add(/[\u1F600-\u1F6FF]/.test(content) ? "text-message" : "emoji-message");

    bubble.append(img, span);
    return bubble;
}

function removeMessageWithFade(el) {
    el.classList.add("fade-out");
    setTimeout(() => el.remove(), 500);
}

function limitMessages() {
    const display = $id("message-display");
    const bubbles = display.getElementsByClassName("message-bubble");
    if (bubbles.length >= MAX_MESSAGES) removeMessageWithFade(bubbles[0]);
}

function sendMessage(content, id) {
    if (!content.trim()) return;
    const display = $id("message-display");
    limitMessages();
    const bubble = createMessageBubble(content, imagePath);

    $.post(site_url + 'playings/messageSubmit', { message: content }, (data) => {
        if (data.status === 'success') {
            display.appendChild(bubble);
            messagesDisplayed.push(id || data.id);
            scrollToBottom();
            $('#message-send-new').val('');
            setTimeout(() => removeMessageWithFade(bubble), 3000);
        }
    });
}

function sendEmoji(content, id) {
    if (!content.trim()) return;
    const display = $id("message-display");
    limitMessages();
    const bubble = createMessageBubble(content, imagePath);

    $.post(site_url + 'playings/messageSubmit', { message: content }, (data) => {
        if (data.status === 'success') {
            display.appendChild(bubble);
            messagesDisplayed.push(id || data.id);
            scrollToBottom();
            $('#message-send-new').val('');
            setTimeout(() => removeMessageWithFade(bubble), 3000);
        }
    });
}

function pollMessages() {
    $.get(site_url + 'playings/messageGet', (data) => {
        if (data.status === 'stop') return;
        if (data.status === 'success' && !messagesDisplayed.includes(data.message.id)) {
            const bubble = createMessageBubble(data.message.message, data.image);
            $id("message-display").appendChild(bubble);
            messagesDisplayed.push(data.message.id);
            scrollToBottom();
            setTimeout(() => removeMessageWithFade(bubble), 3000);
        }
    }).always(() => setTimeout(pollMessages, POLL_INTERVAL));
}

let winners = [];
let winnerIndex = 0;
let winnerSliderTimeout;

function showCountdown(data, callback) {
    const numberHe = $id('countdown');
    const container = $id('countdown-container');
    const textHe = $id('text-countdown');
    let countdown = 5;

    container.style.display = 'block';
    numberHe.textContent = __['bingo!'];
    textHe.innerHTML = `${data.modality}<br />${data.player}`;
    numberHe.style.color = 'white';

    const nextGameSpan = document.querySelector('.next-game');

    if (!winners.some(w => w.player === data.player && w.modality === data.modality)) {
        winners.push({ player: data.player, modality: data.modality });
    }

    function startWinnerSlider() {
        if (winners.length === 0) return;

        clearTimeout(winnerSliderTimeout); 

        function showNext() {
            const current = winners[winnerIndex];
            nextGameSpan.textContent = `GANADOR: ${current.player} - ${current.modality}`;
            winnerIndex = (winnerIndex + 1) % winners.length;
            winnerSliderTimeout = setTimeout(showNext, 5000); 
        }

        showNext();
    }

    startWinnerSlider();

    if (!soundWinner) soundWinner = new Audio();
    soundWinner.src = audioPath + 'winning.mp3';
    soundWinner.play();

    const cartn = $id(`modality-${data.modalityId}`);
    if (cartn) {
        cartn.classList.add('cartn-sing');
        cartn.querySelectorAll('.card-number.modality-sing').forEach(el => {
            el.classList.add('sing');
            el.innerText = '⭐️';
        });
    }

    setTimeout(() => {
        numberHe.style.backgroundImage = `url(${data.image})`;
        numberHe.style.backgroundSize = 'cover';
        numberHe.style.backgroundPosition = 'center';
        numberHe.style.color = 'transparent';

        setTimeout(() => {
            numberHe.style.backgroundImage = '';
            numberHe.style.background = 'linear-gradient(145deg, #6236ff, #8767fa)';
            numberHe.style.color = 'white';
            numberHe.textContent = countdown;

            const interval = setInterval(() => {
                numberHe.textContent = --countdown;
                if (countdown === 0) {
                    clearInterval(interval);
                    container.style.display = 'none';
                    if (callback) callback();
                }
            }, 1000);
        }, 3000);
    }, 2000);

    createConfetti();
}

function createConfetti() {
    const emojis = ['🎉', '🎊', '✨', '⭐️', '🥳', '🍾', '💥', '🔥', '💫', '🍬', '🎈'];
    for (let i = 0; i < MAX_CONFETTI; i++) {
        const confetti = $('<div class="confetti"></div>').text(emojis[Math.floor(Math.random() * emojis.length)]);
        confetti.css({
            left: Math.random() * 150 + 'vw',
            top: Math.random() * -100 + 'vh',
            fontSize: (Math.random() * 30 + 10) + 'px',
            animationDuration: (Math.random() * 5 + 1) + 's',
            animationDelay: Math.random() + 's'
        });
        $('body').append(confetti);
        confetti.on('animationend', function () { $(this).remove(); });
    }
}

function updateBallsCounter(totalNumbersGenerated) {
    const totalBalls = 75;
    const drawn = totalNumbersGenerated;
    const remaining = totalBalls - drawn;
    $('#balls-counter').text(`${drawn} - ${remaining}`);

    const nextGameSpan = document.querySelector('.next-game');

    if (drawn === 1) {
        if (intervalNextGame) {
            clearInterval(intervalNextGame);
            intervalNextGame = null;
        }
        nextGameSpan.textContent = '¡EL JUEGO HA INICIADO!';
    }
}

function handleNewNumber(newNumber, totalNumbersGenerated) {
    if (numbersgenerated.includes(newNumber)) return;

    updateBallsCounter(totalNumbersGenerated);

    numbersgenerated.push(newNumber);
    const el = $id('number-' + newNumber);
    if (el) el.removeAttribute('onclick');

    if (!lastNumbers.includes(newNumber)) {
        setTimeout(() => {
            setTimeout(() => {
                lastNumbers.push(newNumber);
                if (lastNumbers.length > 5) lastNumbers.shift();
                const latestUncurrent = lastNumbers.slice(0, -1);
                const container = $("#last-five-numbers").empty();
                latestUncurrent.forEach(num => {
                    container.append(`<div class="bingo-ball ${getColumnClass(num)} size-40"><span>${num}</span></div>`);
                });
            }, 1000);
            $('#last-number').html('<small style="position: absolute; top: -13px; font-size: 1.2rem; z-index: 1;">' + getColumnClass(newNumber) + '</small><span>' + newNumber + '</span>').removeClass().addClass('bingo-ball ' + getColumnClass(newNumber) + ' size-100');
        }, 1500);
    }

    if (!lastNumbers.includes(newNumber)) {
        if (narrationPlaying) {
            if (!narrationAudio) narrationAudio = new Audio();
            narrationAudio.src = audioPath + newNumber + '.mp3';
            narrationAudio.play();
        }

        setTimeout(() => {
            $('#last-number').removeClass("move-number");
        }, 1000);

        if (autoMarkEnabled) {
            dialNumber(newNumber);
        }
    }

    $("#board-number-" + newNumber).addClass(getColumnClass(newNumber));
}

function lastNumberGet() {
    $.get(site_url + 'playings/numberGet', (data) => {
        if (data.status === 'pause') {
            clearInterval(lastInterval);
            showCountdown(data, startAutomaticLast);
        } else if (data.status === 'success') {
            handleNewNumber(data.number, data.totalNumbersGenerated)
        } else if (data.status === 'completed') {
            clearInterval(lastInterval);
            stopAutomaticLast();
            setTimeout(showGameFinalized, timeBallGet);
        }
    });
}

function startAutomaticLast() {
    clearInterval(lastInterval);
    lastInterval = setInterval(lastNumberGet, timeBallLast);
}

function stopAutomaticLast() {
    clearInterval(lastInterval);
}

function showGameFinalized() {
    if (isGameFinishedShown) return;
    isGameFinishedShown = true;
    const container = $id('game-finalized');
    const text = $id('finalized');
    container.style.display = 'block';
    text.innerHTML = __['game finished!'];
    setTimeout(() => {
        awardsGet();
        container.style.display = 'none';
    }, 5000);

    stopAutomaticGeneration();
    stopAutomaticLast();
    stopupdateUserCount();

    let divToRemove = document.getElementById('controls');
    if (divToRemove) {
        divToRemove.remove();
    }
}

function updateUserCount() {
    $.get(site_url + 'boards/playersGetCount', (data) => {
        if (data.userCount && data.userCount > 0) {
            $('.count_notifications').text(data.userCount).show();
        } else {
            $('.count_notifications').hide();
        }
    });
}

function stopupdateUserCount() {
    if (userCountInterval) {
        clearInterval(userCountInterval);
    }
}

function dialNumber(number) {
    let elementsNumber = $(".number-" + number);
    if (elementsNumber.length) {
        $.ajax({
            url: site_url + 'playings/dialNumber',
            method: 'POST',
            data: {
                number: number
            },
            success: function(data) {
                if (data.status === 'success') {
                    elementsNumber.each(function() {
                        let elementNumber = $(this);

                        if (elementNumber.hasClass('marked')) return;

                        const originalContent = elementNumber.text();

                        elementNumber.text('⭐️').addClass('explosive-effect');

                        setTimeout(function() {
                            elementNumber.text(originalContent); 
                            elementNumber.removeClass('explosive-effect');
                            elementNumber.addClass('marked'); 
                        }, 1000); 
                    });
                } else {
                    console.warn("Respuesta no exitosa:", data.message || data);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en AJAX al marcar número:", number, error);
            }
        });
    }
}

function dialNumber(number) {
    let elementsNumber = $(".number-" + number);

    if (!elementsNumber.length) {
        console.warn("No se encontró el número en el DOM:", number);
        return;
    }

    $.ajax({
        url: site_url + 'playings/dialNumber',
        method: 'POST',
        data: { number: number },
        success: function(data) {
            if (data.status === 'success') {
                elementsNumber.each(function() {
                    let elementNumber = $(this);

                    // Evita remarcar si ya está marcado
                    if (elementNumber.hasClass('marked')) return;

                    const originalContent = elementNumber.text();

                    elementNumber.text('⭐️').addClass('explosive-effect');

                    setTimeout(function() {
                        elementNumber.text(originalContent); 
                        elementNumber.removeClass('explosive-effect');
                        elementNumber.addClass('marked'); 
                    }, 1000);
                });
            } else {
                console.warn("Respuesta no exitosa:", data.message || data);
            }
        },
        error: function(xhr, status, error) {
            console.error("Error en AJAX al marcar número:", number, error);
        }
    });
}

function singBingo() {
    const bingoButton = document.querySelector('.btn-bingooo');
    if (bingoButton) {
        bingoButton.classList.remove('animate-click');
        void bingoButton.offsetWidth;
        bingoButton.classList.add('animate-click');
    }

    $.ajax({
        url: site_url + 'playings/singBingo',
        method: 'POST',
        success: function(data) {
            if (data.status === 'success') {
                clearInterval(lastInterval);

                sendMessage(__['bingo!'] + ' 🥳', 21);

                let countdownNumberHe = document.getElementById('countdown');
                let countdownContainer = document.getElementById('countdown-container');
                let countdownTextHe = document.getElementById('text-countdown');
                let countdown = 5;

                let userImagePath = data.image;
                let userName = data.player;
                let modalityName = data.modality;

                countdownContainer.style.display = 'block';

                countdownNumberHe.textContent = __['bingo!'];
                countdownTextHe.innerHTML = modalityName + '<br />' + userName;
                countdownNumberHe.style.color = 'white';

                const cartonElement = document.getElementById(`carton-${data.carton}`);
                if (cartonElement) {
                    data.numbers.forEach(num => {
                        const numberElement = cartonElement.querySelector(`.bingo-carton-number.number-${num}`);
                        if (numberElement) {
                            numberElement.classList.add('carton-sing');
                        }
                    });
                }

                const nextGameSpan = document.querySelector('.next-game');

                if (!winners.some(w => w.player === data.player && w.modality === data.modality)) {
                    winners.push({ player: data.player, modality: data.modality });
                }

                function startWinnerSlider() {
                    if (winners.length === 0) return;

                    clearTimeout(winnerSliderTimeout); 

                    function showNext() {
                        const current = winners[winnerIndex];
                        nextGameSpan.textContent = `GANADOR: ${current.player} - ${current.modality}`;
                        winnerIndex = (winnerIndex + 1) % winners.length;
                        winnerSliderTimeout = setTimeout(showNext, 5000);
                    }

                    showNext();
                }

                startWinnerSlider();

                if (!soundWinner) soundWinner = new Audio();
                soundWinner.src = audioPath + 'winning.mp3';
                soundWinner.play();

                const cartn = $id(`modality-${data.modalityId}`);
                if (cartn) {
                    cartn.classList.add('cartn-sing');
                    cartn.querySelectorAll('.card-number.modality-sing').forEach(el => {
                        el.classList.add('sing');
                        el.innerText = '⭐️';
                    });
                }

                setTimeout(() => {
                    countdownNumberHe.style.backgroundImage = `url(${userImagePath})`;
                    countdownNumberHe.style.backgroundSize = 'cover';
                    countdownNumberHe.style.backgroundPosition = 'center';
                    countdownNumberHe.style.color = 'transparent';

                    setTimeout(() => {
                        countdownNumberHe.style.backgroundImage = '';
                        countdownNumberHe.style.background = 'linear-gradient(145deg, #6236ff, #8767fa)';
                        countdownNumberHe.style.color = 'white'; 
                        countdownNumberHe.textContent = countdown; 

                        let countdownInterval = setInterval(() => {
                            countdown--;
                            countdownNumberHe.textContent = countdown;

                            if (countdown === 0) {
                                clearInterval(countdownInterval);
                                countdownContainer.style.display = 'none'; 
                                startAutomaticLast(); 
                            }
                        }, 1000);
                    }, 3000); 
                }, 2000); 

                createConfetti();
            }
        }
    });
}

$(".card-number").click(function() {
    if (!autoMarkEnabled) {
        let number = $(this).data('number'); 
        dialNumber(number);  
    }
});

function setupEvents() {
    $('#message-button').on('click', () => sendMessage($('#message-send-new').val()));
    $('#message-send-new').on('keypress', (e) => { if (e.which === 13) sendMessage($('#message-send-new').val()); });
    $('.btn-microphone').on('click', function () {
        narrationPlaying = !narrationPlaying;
        $(this).html(narrationPlaying ? '<i class="fa-duotone fa-solid fa-microphone"></i>' : '<i class="fa-duotone fa-solid fa-microphone-slash"></i>');
    });
    $('.btn-binary').click(function() {
        autoMarkEnabled = !autoMarkEnabled;
        $(this).html(autoMarkEnabled ? '<i class="fa-duotone fa-solid fa-binary-circle-check"></i>' : '<i class="fa-duotone fa-solid fa-binary-slash"></i>');
    });
    $('.modal').on("hidden.bs.modal", function (e) {
        if($('.modal:visible').length)
        {
            $('.modal-backdrop').first().css('z-index', parseInt($('.modal:visible').last().css('z-index')) - 10);
            $('body').addClass('modal-open');
        }
    }).on("show.bs.modal", function (e) {
        if($('.modal:visible').length)
        {
            $('.modal-backdrop.in').first().css('z-index', parseInt($('.modal:visible').last().css('z-index')) + 10);
            $(this).css('z-index', parseInt($('.modal-backdrop.in').first().css('z-index')) + 10);
        }
    });

    $id("toggle-messages-btn").addEventListener("click", function (event) {
        const messageContainer = $id("message-display-container");
        messageContainer.style.display = messageContainer.style.display === "flex" ? "none" : "flex";
        event.stopPropagation();
    });
    document.addEventListener("click", function (event) {
        const messageContainer = $id("message-display-container");
        const toggleButton = $id("toggle-messages-btn");
        if (messageContainer.style.display === "flex" && !messageContainer.contains(event.target) && !toggleButton.contains(event.target)) {
            messageContainer.style.display = "none";
        }
    });

    document.addEventListener("DOMContentLoaded", function () {  
        const container = document.querySelector(".board-section");

        function isMobile() {
            return window.innerWidth <= 700;
        }

        function isTablet() {
            return window.innerWidth >= 701 && window.innerWidth <= 1024;
        }

        function shouldApplyMask() {
            if (isMobile()) return true;
            if (isTablet()) return true;
            return false;
        }

        function updateMask() {
            setTimeout(() => {
                const scrollTop = container.scrollTop;
                const scrollHeight = container.scrollHeight;
                const clientHeight = container.clientHeight;

                if (!shouldApplyMask()) {
                    container.style.maskImage = "none";
                    container.style.webkitMaskImage = "none";
                    return;
                }

                if (scrollHeight <= clientHeight) {
                    container.style.maskImage = "none";
                    container.style.webkitMaskImage = "none";
                    return;
                }

                if (scrollTop === 0) {
                    container.style.maskImage = "linear-gradient(to bottom, rgba(0, 0, 0, 1) 0%, rgba(0, 0, 0, 1) 80%, rgba(0, 0, 0, 0) 100%)";
                    container.style.webkitMaskImage = "linear-gradient(to bottom, rgba(0, 0, 0, 1) 0%, rgba(0, 0, 0, 1) 80%, rgba(0, 0, 0, 0) 100%)";
                } else if (scrollTop + clientHeight >= scrollHeight) {
                    container.style.maskImage = "linear-gradient(to top, rgba(0, 0, 0, 1) 0%, rgba(0, 0, 0, 1) 80%, rgba(0, 0, 0, 0) 100%)";
                    container.style.webkitMaskImage = "linear-gradient(to top, rgba(0, 0, 0, 1) 0%, rgba(0, 0, 0, 1) 80%, rgba(0, 0, 0, 0) 100%)";
                } else {
                    container.style.maskImage = "linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 1) 15%, rgba(0, 0, 0, 1) 80%, rgba(0, 0, 0, 0) 100%)";
                    container.style.webkitMaskImage = "linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 1) 15%, rgba(0, 0, 0, 1) 80%, rgba(0, 0, 0, 0) 100%)";
                }
            }, 50);
        }
        
        container.addEventListener("scroll", updateMask);
        window.addEventListener("resize", updateMask);
        updateMask(); 
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const nextGameSpan = document.querySelector('.next-game');
    const targetDate = new Date(gameDate);
    const now = new Date();

    let winnerIndex = 0;

    function updateCountdown() {
        const now = new Date();
        const timeDiff = targetDate - now;

        if (timeDiff <= 0) {
            clearInterval(intervalNextGame);

            if (totalNumbersGenerated > 0) {
                if (winners.length > 0) {
                    startWinnerSlider();
                } else {
                    nextGameSpan.textContent = '¡EL JUEGO HA INICIADO!';
                }
            } else {
                nextGameSpan.textContent = 'ESPERE QUE INICIE LA PARTIDA...';
            }

            return;
        }

        const days = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);

        let text = '';
        if (days > 0) {
            text = `EL JUEGO INICIA EN: ${days} DÍA${days > 1 ? 'S' : ''} ${hours} HORA${hours > 1 ? 'S' : ''} - ${minutes}:${seconds < 10 ? '0' : ''}${seconds} MIN`;
        } else if (hours > 0) {
            text = `EL JUEGO INICIA EN: ${hours} HORA${hours > 1 ? 'S' : ''} - ${minutes}:${seconds < 10 ? '0' : ''}${seconds} MIN`;
        } else {
            text = `EL JUEGO INICIA EN: ${minutes}:${seconds < 10 ? '0' : ''}${seconds} MINUTO${minutes > 1 ? 'S' : ''}`;
        }

        nextGameSpan.textContent = text;
    }

    function startWinnerSlider() {
        if (winners.length === 0) return;

        nextGameSpan.textContent = `GANADOR: ${winners[winnerIndex].player} - ${winners[winnerIndex].modality}`;
        winnerIndex = (winnerIndex + 1) % winners.length;

        setTimeout(startWinnerSlider, 3000);
    }

    if (now < targetDate) {
        updateCountdown();
        intervalNextGame = setInterval(updateCountdown, 1000);
    } else {
        if (totalNumbersGenerated > 0) {
            if (winners.length > 0) {
                startWinnerSlider();
            } else {
                nextGameSpan.textContent = '¡EL JUEGO HA INICIADO!';
            }
        } else {
            nextGameSpan.textContent = 'ESPERE QUE INICIE LA PARTIDA...';
        }
    }
});

function RemoveVolume() {
    $.ajax({
        url: site_url + 'playings/volumeSubmit',
        method: 'POST',
        success: function(data) {
            if (data.status === 'success') {
                console.log("sound disabled successfully");
            } else {
                console.log("error sending request");
            }
        }
    });
}

function RemoveMicrophone() {
    $.ajax({
        url: site_url + 'playings/microphoneSubmit',
        method: 'POST',
        success: function(data) {
            if (data.status === 'success') {
                console.log("narrator disabled successfully");
            } else {
                console.log("error sending request");
            }
        }
    });
}

function RemoveCheck() {
    $.ajax({
        url: site_url + 'playings/checkSubmit',
        method: 'POST',
        success: function(data) {
            if (data.status === 'success') {
                console.log("automarked disabled successfully");
            } else {
                console.log("error sending request");
            }
        }
    });
}

$(document).ready(() => {
    pollMessages();
    setupEvents();
    userCountInterval = setInterval(updateUserCount, 2500);
    updateUserCount();
    startAutomaticLast();
});