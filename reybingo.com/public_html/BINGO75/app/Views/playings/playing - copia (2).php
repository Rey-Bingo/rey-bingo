<style>
    .column {
        display: grid;
        grid-template-columns: repeat(16, 1fr);
        gap: 5px;
        text-align: center;
    }

    #last-numbers {
        display: grid;
        gap: 5px;
        text-align: center;
        position: fixed;
        bottom: 25%;
        right: 25px;
    }

    .ball-number {
        background-color: #ffffff;        
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        position: relative;
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.5), inset 0 -5px 10px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin-bottom: 5px;
    }

    .ball-number span {
        font-weight: bold;
        color: #535560;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        position: absolute;
        z-index: 2;
    }

    .ball-number::before {
        content: '';
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.5)  0%, rgba(255, 255, 255, 0) 70%);
        z-index: 1;
    }

    .ball-25 { 
        width: 25px;
        height: 25px;
        border: 3.5px solid #909090;
    }

    .ball-25 span {
        width: 15px;
        height: 15px;
        font-size: 0.5rem;
    }

    .ball-25::before {
        width: 15px;
        height: 15px;
        border: 1px solid #909090;
    }

    .ball-30 { 
        width: 30px;
        height: 30px;
        border: 4px solid #909090;
    }

    .ball-30 span {
        width: 18px;
        height: 18px;
        font-size: 0.7rem;
    }

    .ball-30::before {
        width: 20px;
        height: 20px;
        border: 1.5px solid #909090;
    }

    .ball-50 { 
        width: 50px;
        height: 50px;
        border: 6px solid #909090;
    }

    .ball-50 span {
        width: 28px;
        height: 28px;
        font-size: 1rem;
    }

    .ball-50::before {
        width: 30px;
        height: 30px;
        border: 2.5px solid #909090;
    }

    .ball-40 {
        width: 30px;
        height: 30px;
        border: 3.5px solid #909090;
    }

    .ball-40 span {
        width: 19px;
        height: 19px;
        font-size: 0.7rem;
    }

    .ball-40::before {
        width: 19px;
        height: 19px;
        border: 1px solid #909090;
    }

    .ball-60 {
        width: 45px;
        height: 45px;
        border: 5px solid #909090;
    }

    .ball-60 span {
        width: 28px;
        height: 28px;
        font-size: 1rem;
    }

    .ball-60::before {
        width: 30px;
        height: 30px;
        border: 2px solid #909090;
    }

    .ball-90 {
        width: 70px;
        height: 70px;
        border: 6px solid #909090;
    }

    .ball-90 span {
        width: 40px;
        height: 40px;
        font-size: 1.3rem;
    }

    .ball-90::before {
        width: 50px;
        height: 50px;
        border: 3px solid #909090;
    }

    .b-col {
        border-color: #fa5c93;
    }

    .b-col::before {
        border-color: #fa5c93;
    }

    .i-col {
        border-color: #f9ac0a;
    }

    .i-col::before {
        border-color: #f9ac0a;
    }

    .n-col {
        border-color: #fbd60d;
    }

    .n-col::before {
        border-color: #fbd60d;
    }

    .g-col {
        border-color: #00ba71;
    }

    .g-col::before {
        border-color: #00ba71;
    }

    .o-col {
        border-color: #22cbe6;
    }

    .o-col::before {
        border-color: #22cbe6;
    }

    @media (max-width:767px) {
        .board-number {
            display: flex;
            justify-content: center;
            gap: 5px;
            max-width: 90%;
            margin: 0 auto;
            position: relative;
            top: -20px;
        }

        .column {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0px;
        }

        #last-numbers {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 5px;
            text-align: center;
            position: fixed;
            bottom: 100px;
            left: 20%;
            width: 50%;
        }

        .ball-25 span {
            width: 15px;
            height: 15px;
            font-size: 0.5rem;
            line-height: 2;
        }
    }

    /* Configuración del contenedor de confetti */
    /*.confetti {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        pointer-events: none;
        z-index: 9999;
        overflow: hidden;
    }

    .confetti-uno {
        position: absolute;
        width: 10px;
        height: 10px;
        background-color: var(--color, #ff0);
        animation: fall linear infinite;
        opacity: 0;
    }

    @keyframes fall {
        0% {
            transform: translateY(-100px) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(110vh) rotate(360deg);
            opacity: 0;
        }
    }

    .confetti-dos {
        position: absolute;
        width: 10px;
        height: 10px;
        background-color: var(--color, #ff0);
        border-radius: 50%;
        animation: explode 1s ease-out forwards;
    }

    @keyframes explode {
        0% {
            transform: translate(0, 0) scale(1);
            opacity: 1;
        }
        100% {
            transform: translate(var(--x, 0), var(--y, 0)) scale(0.5);
            opacity: 0;
        }
    }*/

    .confetti {
        position: fixed; /* Asegura que el confeti esté fijo en la ventana */
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        pointer-events: none;
        z-index: 9999;
        opacity: 0.8;
        animation: explode 3s forwards;
    }

    @keyframes explode {
        0% {
            transform: translateY(-100vh) rotate(0deg); /* Comienza fuera del viewport */
        }
        100% {
            transform: translateY(100vh) rotate(720deg); /* Cae a través del viewport */
        }
    }

    .countdown-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9998;
        pointer-events: none;
    }

    #countdown {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 150px;
        height: 150px;
        background: linear-gradient(145deg, #FF66C4, #ff9800);
        color: white;
        font-size: 64px;
        font-weight: bold;
        border-radius: 50%;
        border: 5px solid #ffffff;
        box-shadow: 6px 6px 15px rgba(255, 183, 77, 0.2), -6px -6px 15px rgba(255, 152, 0, 0.3);
        position: relative;
        animation: pulse 1s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
        100% {
            transform: scale(1);
        }
    }

    @keyframes burst {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1) rotate(360deg);
            opacity: 0.5;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .explosive-effect {
        font-size: 1.5rem;
        animation: burst 0.6s ease-in-out;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(720deg);
        }
    }
</style>

<style>
    .bingo-container {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 15px;
        width: 100%;
        max-width: 1200px;
    }

    .bingo-carton {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 5px;
        background-color: #fff;
        border-radius: 15px;
        padding: 5px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        text-align: center;
        width: 100%;
    }

    .bingo-border-carton {
        padding: 5px;
        background-color: rgba(255, 255, 255, 0.5);
        border-radius: 20px;
    }

    .bingo-carton-number {
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #ffe6f2;
        font-size: 1.2vw;
        font-weight: bold;
        color: #535560;
        border-radius: 5px;
        transition: transform 0.3s ease;
        width: 35px;
        height: 35px;
    }

    .bingo-carton-header {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: bold;
        color: #ffffff;
        border-radius: 5px;
        transition: transform 0.3s ease;
        width: 35px;
        height: 35px;
    }

    .single-carton {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .marked {
        background-color: #ff7000 !important;
        color: #ffffff !important;
    }

    .six-cartons {
        height: 90%;
        overflow: auto;
    }

    .six-cartons::-webkit-scrollbar {
        -webkit-appearance: none !important;
    }

    .six-cartons::-webkit-scrollbar:vertical {
        width: 6px !important;
    }

    .six-cartons::-webkit-scrollbar-button:increment,.six-cartons::-webkit-scrollbar-button {
        display: none !important;
    } 

    .six-cartons::-webkit-scrollbar:horizontal {
        height: 6px !important;
    }

    .six-cartons::-webkit-scrollbar-thumb {
        background-color: #ADADAD !important;
        border-radius: 7px !important;
        border: 1px solid #EFEFEF !important;
    }

    .six-cartons::-webkit-scrollbar-track {
        background-color: #EFEFEF !important;
    }

    /* 📱 Ajustes para móviles */
    @media (max-width: 500px) {
        .tpt {
            padding-top: 50% !important;
        }

        .container-cartons {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 5px; /* Reducir espacio */
            overflow: auto !important;
            height: 68%;
        }

        .container-cartons.five-cartons {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 5px; /* Reducir espacio */
            overflow: auto !important;
            height: 68%;
        }

        .container-cartons.more-cartons {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 5px;
            overflow: auto !important;
            height: auto !important;
        }

        .more-cartons .bingo-carton {
            max-width: 180px; /* Ajustar para móviles */
            padding: 5px;
        }

        .more-cartons .bingo-border-carton {
            max-height: 210px; /* Ajustar para móviles */
        }

        .more-cartons .bingo-carton-number {
            font-size: 3.5vw;  /* Ajustar tamaño de texto */
            width: 7vw;  /* Ajustar tamaño de los números */
            height: 7vw;
        }

        .more-cartons .bingo-carton-header {
            font-size: 3.5vw;
            width: 7vw;  /* Ajustar tamaño de los números */
            height: 7vw;
        }

        .five-cartons .bingo-carton {
            max-width: 180px; /* Ajustar para móviles */
            padding: 5px;
        }

        .five-cartons .bingo-border-carton {
            max-height: 210px; /* Ajustar para móviles */
        }

        .five-cartons .bingo-carton-number {
            font-size: 3.5vw;  /* Ajustar tamaño de texto */
            width: 7vw;  /* Ajustar tamaño de los números */
            height: 7vw;
        }

        .five-cartons .bingo-carton-header {
            font-size: 3.5vw;
            width: 7vw;  /* Ajustar tamaño de los números */
            height: 7vw;
        }

        .single-carton {
            height: auto !important;
        }

        .single-carton .bingo-border-carton {
            padding: 5px;
            background-color: rgba(255, 255, 255, 0.5);
            border-radius: 20px;
        }

        .single-carton .bingo-carton-number {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #ffe6f2;
            font-size: 1.2rem;
            font-weight: bold;
            color: #535560;
            border-radius: 5px;
            transition: transform 0.3s ease;
            width: 35px;
            height: 35px;
        }

        .single-carton .bingo-carton-header {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffffff;
            border-radius: 5px;
            transition: transform 0.3s ease;
            width: 35px;
            height: 35px;
        }
    }

    /* 📟 Ajustes para tabletas (768px a 1024px) */
    @media (min-width: 501px) and (max-width: 1024px) {
        #last-numbers {
            display: grid;
            gap: 5px;
            text-align: center;
            position: fixed;
            bottom: 8.5%;
            left: 25% !important;
            justify-content: center;
        }

        .container-cartons {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 5px; /* Reducir espacio */
            overflow: auto !important;
            height: 88%;
        }

        .container-cartons.five-cartons {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 5px; /* Reducir espacio */
            overflow: auto !important;
            height: 88%;
        }

        .container-cartons.more-cartons {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 5px !important;
            overflow: auto !important;
            height: auto !important;
        }

        .more-cartons .bingo-carton-number {
            font-size: 3.5vw !important;  /* Ajustar tamaño de texto */
            width: 7vw !important;  /* Ajustar tamaño de los números */
            height: 7vw !important;
        }

        .more-cartons .bingo-carton-header {
            font-size: 3.5vw !important;
            width: 7vw !important;  /* Ajustar tamaño de los números */
            height: 7vw !important;
        }

        .five-cartons .bingo-carton-number {
            font-size: 3.5vw !important;  /* Ajustar tamaño de texto */
            width: 7vw !important;  /* Ajustar tamaño de los números */
            height: 7vw !important;
        }

        .five-cartons .bingo-carton-header {
            font-size: 3.5vw !important;
            width: 7vw !important;  /* Ajustar tamaño de los números */
            height: 7vw !important;
        }

        .single-carton {
            height: auto !important;
        }

        .single-carton .bingo-border-carton {
            padding: 5px !important;
            background-color: rgba(255, 255, 255, 0.5) !important;
            border-radius: 20px !important;
        }

        .single-carton .bingo-carton-number {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background-color: #ffe6f2 !important;
            font-size: 1.2rem !important;
            font-weight: bold !important;
            color: #535560 !important;
            border-radius: 5px !important;
            transition: transform 0.3s ease !important;
            width: 35px !important;
            height: 35px !important;
        }

        .single-carton .bingo-carton-header {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 1.5rem !important;
            font-weight: bold !important;
            color: #ffffff !important;
            border-radius: 5px !important;
            transition: transform 0.3s ease !important;
            width: 35px !important;
            height: 35px !important;
        }
    }
</style>

<link href="<?= site_url('assets/plugin/components/font-awesome/css/fontawesome.min.css'); ?>?<?= md5(date("Hms")); ?>" rel="stylesheet">
<link href="<?= site_url('assets/plugin/czm-chat-support.css'); ?>?<?= md5(date("Hms")); ?>" rel="stylesheet">

<div class="top">
    <div class="controls-container">
        <button type="button" class="btn btn-primary controls-bingo music">
            <i class="fa-duotone fa-solid fa-tv-music"></i>
        </button>

        <button class="btn btn-primary controls-bingo logout" onclick="GoToPage('logout');">
            <i class="fa-duotone fa-solid fa-arrow-right-from-arc"></i>
        </button>

        <button type="button" class="btn btn-primary controls-bingo volume" onclick="RemoveVolume();">
            <i class="fa-duotone fa-solid fa-volume-slash"></i>
        </button>

        <button type="button" class="btn btn-primary controls-bingo microphone" onclick="RemoveMicrophone();">
            <?php if ($user['narration'] == 1): ?>
                <i class="fa-duotone fa-solid fa-microphone"></i>
            <?php else : ?>
                <i class="fa-duotone fa-solid fa-microphone-slash"></i>
            <?php endif; ?>
        </button>

        <button type="button" class="btn btn-primary controls-bingo check" onclick="RemoveCheck();">
            <?php if ($user['autodial'] == 1): ?>
                <i class="fa-duotone fa-solid fa-binary-circle-check"></i>
            <?php else : ?>
                <i class="fa-duotone fa-solid fa-binary-slash"></i>
            <?php endif; ?>
        </button>
    </div>

    <button type="button" class="btn btn-primary controls-bingo user">
        <div class="profile">
            <a class="small fs-6 linkPage" href="<?= site_url('profile'); ?>"><img src="<?= $imagePath ?>" alt="img"></a>
        </div>
    </button>

    <button type="button" class="btn btn-primary controls-bingo board" data-bs-toggle="modal" data-bs-target="#modalities">
        <i class="fa-duotone fa-solid fa-chess-board"></i>
    </button>

    <button type="button" class="btn btn-primary controls-bingo table" data-bs-toggle="modal" data-bs-target="#board">
        <i class="fa-duotone fa-solid fa-table-cells"></i>
    </button>

    <button type="button" class="btn btn-primary controls-bingo award" onclick="awardsGet();">
        <i class="fa-duotone fa-solid fa-award"></i>
    </button>

    <button type="button" class="btn btn-primary controls-bingo wallet" onclick="paymentsGet();">
        <i class="fa-duotone fa-solid fa-wallet"></i>
    </button>

    <h1 class="fs-5 text-center tpt"><?= $game['description']; ?></h1>
</div>

<div class="container-cartons">
    <?php if (isset($cartons) && count($cartons) > 0): ?>
        <?php foreach ($cartons as $cartonData): ?>
            <div class="bingo-border-carton">
                <div class="bingo-carton" id="carton-<?= $cartonData['cartonId']; ?>">
                    <div class="bingo-carton-header b-col"><span>B</span></div>
                    <div class="bingo-carton-header i-col"><span>I</span></div>
                    <div class="bingo-carton-header n-col"><span>N</span></div>
                    <div class="bingo-carton-header g-col"><span>G</span></div>
                    <div class="bingo-carton-header o-col"><span>O</span></div>
                    
                    <?php foreach ($cartonData['numbers'] as $index => $number): ?>
                        <?php if ($index === 12): ?>
                            <div class="bingo-carton-number" data-position="<?= $number['position']; ?>">
                                <img src="<?= site_url('assets/img/3.gif'); ?>" class="img-fluid img-carton" alt="img">
                            </div>
                        <?php else: ?>
                            <div class="bingo-carton-number number-<?= $number['number']; ?> <?php if ($number['status'] == 1): ?>marked<?php endif; ?>" data-position="<?= $number['position']; ?>" id="number-<?= $number['number']; ?>" onclick="dialNumber(<?= $number['number']; ?>);"><?= $number['number']; ?></div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p><?= translate('there are no cards available for this game'); ?></p>
    <?php endif; ?>
</div>

<div class="bottom">
    <div id="confetti-container" class="confetti"></div>

    <div id="last-numbers"></div>

    <button type="button" class="btn btn-primary controls-bingo message" id="toggle-messages-btn">
        <i class="fa-duotone fa-solid fa-message-smile"></i>
    </button>

    <div class="message-display-container" id="message-display-container">

        <div class="message-display" id="message-display"></div>

        <div class="emoji-message-panel">
            <div class="emoji-slider">
                <button type="button" class="emoji-btn" onclick="sendEmoji('😊', 1)">😊</button>
                <button type="button" class="emoji-btn" onclick="sendEmoji('😢', 2)">😢</button>
                <button type="button" class="emoji-btn" onclick="sendEmoji('🤯', 3)">🤯</button>
                <button type="button" class="emoji-btn" onclick="sendEmoji('😂', 4)">😂</button>
                <button type="button" class="emoji-btn" onclick="sendEmoji('😍', 5)">😍</button>
                <button type="button" class="emoji-btn" onclick="sendEmoji('🥺', 6)">🥺</button>
                <button type="button" class="emoji-btn" onclick="sendEmoji('🤔', 7)">🤔</button>
                <button type="button" class="emoji-btn" onclick="sendEmoji('🙄', 8)">🙄</button>
                <button type="button" class="emoji-btn" onclick="sendEmoji('🧐', 9)">🧐</button>
                <button type="button" class="emoji-btn" onclick="sendEmoji('😘', 10)">😘</button>
                <button type="button" class="emoji-btn" onclick="sendEmoji('😜', 11)">😜</button>
                <button type="button" class="emoji-btn" onclick="sendEmoji('😅', 12)">😅</button>
                <button type="button" class="emoji-btn" onclick="sendEmoji('😨', 13)">😨</button>
                <button type="button" class="emoji-btn" onclick="sendEmoji('😎', 14)">😎</button>
                <button type="button" class="emoji-btn" onclick="sendEmoji('🤪', 15)">🤪</button>
                <button type="button" class="emoji-btn" onclick="sendEmoji('😲', 16)">😲</button>
                <button type="button" class="emoji-btn" onclick="sendEmoji('😒', 17)">😒</button>
                <button type="button" class="emoji-btn" onclick="sendEmoji('😛', 18)">😛</button>
                <button type="button" class="emoji-btn" onclick="sendEmoji('😓', 19)">😓</button>
            </div>

            <div class="message-bubble-slider">
                <button type="button" class="message-btn" onclick="sendMessage('<?= translate('hello!'); ?> 🥰', 20)"><?= translate('hello!'); ?> 🥰</button>
                <button type="button" class="message-btn" onclick="sendMessage('<?= translate('lets have fun!'); ?> 😉', 21)"><?= translate('lets have fun!'); ?> 😉</button>
                <button type="button" class="message-btn" onclick="sendMessage('<?= translate('bingo!'); ?> 🥳', 22)"><?= translate('bingo!'); ?> 🥳</button>
                <button type="button" class="message-btn" onclick="sendMessage('<?= translate('ha ha ha'); ?> 🤣', 23)"><?= translate('ha ha ha'); ?> 🤣</button>
                <button type="button" class="message-btn" onclick="sendMessage('<?= translate('good luck!'); ?> 🤑', 24)"><?= translate('good luck!'); ?> 🤑</button>
                <button type="button" class="message-btn" onclick="sendMessage('<?= translate('im missing a number'); ?> 🤩', 25)"><?= translate('im missing a number'); ?> 🤩</button>
            </div>

            <div class="input-group">
                <input type="text" class="form-control form-control-chat input-bingo" name="message-send-new" id="message-send-new" placeholder="<?= translate('write a message'); ?>..." autofocus autocomplete="off" maxlength="50">
                <button type="button" id="message-button" class="btn btn-primary btn-send" onclick="sendMessageText()"><i class="fa-duotone fa-solid fa-paper-plane-top"></i></button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalities" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-center"><i class="fa-duotone fa-solid fa-chess-board"></i> <?= translate('modalities'); ?></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="close"><i class="fa-duotone fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body">
                    <?php if (isset($modalities) && count($modalities) > 0): ?>
                    <div class="container-cartons-modalities" style="grid-template-columns: repeat(4, 1fr); width: 220px;">
                        <?php foreach ($modalities as $modality): ?>
                            <div class="border-carton">
                                <div class="carton" id="modality-<?= $modality['id']; ?>" style="width: 100px; height: 130px;">
                                    <div class="card-letter b-col" style="font-size: .6rem; width: 14px; height: 14px;"><span>B</span></div>
                                    <div class="card-letter i-col" style="font-size: .6rem; width: 14px; height: 14px;"><span>I</span></div>
                                    <div class="card-letter n-col" style="font-size: .6rem; width: 14px; height: 14px;"><span>N</span></div>
                                    <div class="card-letter g-col" style="font-size: .6rem; width: 14px; height: 14px;"><span>G</span></div>
                                    <div class="card-letter o-col" style="font-size: .6rem; width: 14px; height: 14px;"><span>O</span></div>

                                    <?php
                                        $positions = explode(',', $modality['positions']);
                                    ?>

                                    <?php for ($i = 1; $i <= 25; $i++): ?>
                                        <?php if ($i == 13): ?>
                                            <div class="card-number" data-position="13" ><img src="<?= site_url('assets/img/3.gif'); ?>" class="img-fluid img-carton" alt="img" style="max-width: 100%; height: auto;"></div>
                                        <?php else: ?>
                                            <div class="card-number <?= in_array($i, $positions) ? 'modality' : ''; ?>" data-position="<?= $i; ?>" style="width: 14px; height: 14px;"></div>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                                <small style="font-size: .7rem;"><?= $modality['name']; ?></small> <br />
                                <span style="font-size: .9rem;"><?= systemGet('currency'); ?> <?= number_format($modality['amount'], 2) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                        <p><?= translate('there are no modalities available'); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="board" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered max-w-50">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-center"><i class="fa-duotone fa-solid fa-table-cells"></i> <?= translate('board'); ?></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="close"><i class="fa-duotone fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body">
                    <br>
                    <div class="board-number">
                        <div class="column">
                            <div class="ball-number ball-25 b-col"><span>B</span></div>
                            <?php foreach (range(1, 15) as $number): ?>
                                <?php $class = in_array($number, $selectedNumbers) ? $getClass($number) : ''; ?>
                                <div class="ball-number ball-25 <?= $class ?>" id="board-number-<?= $number ?>">
                                    <span><?= $number ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="column">
                            <div class="ball-number ball-25 i-col"><span>I</span></div>
                            <?php foreach (range(16, 30) as $number): ?>
                                <?php $class = in_array($number, $selectedNumbers) ? $getClass($number) : ''; ?>
                                <div class="ball-number ball-25 <?= $class ?>" id="board-number-<?= $number ?>">
                                    <span><?= $number ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="column">
                            <div class="ball-number ball-25 n-col"><span>N</span></div>
                            <?php foreach (range(31, 45) as $number): ?>
                                <?php $class = in_array($number, $selectedNumbers) ? $getClass($number) : ''; ?>
                                <div class="ball-number ball-25 <?= $class ?>" id="board-number-<?= $number ?>">
                                    <span><?= $number ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="column">
                            <div class="ball-number ball-25 g-col"><span>G</span></div>
                            <?php foreach (range(46, 60) as $number): ?>
                                <?php $class = in_array($number, $selectedNumbers) ? $getClass($number) : ''; ?>
                                <div class="ball-number ball-25 <?= $class ?>" id="board-number-<?= $number ?>">
                                    <span><?= $number ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="column">
                            <div class="ball-number ball-25 o-col"><span>O</span></div>
                            <?php foreach (range(61, 75) as $number): ?>
                                <?php $class = in_array($number, $selectedNumbers) ? $getClass($number) : ''; ?>
                                <div class="ball-number ball-25 <?= $class ?>" id="board-number-<?= $number ?>">
                                    <span><?= $number ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="wallet" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"></div></div>

    <div class="modal fade" id="payments" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"></div></div>

    <div class="modal fade" id="recharge" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"></div></div>

    <div class="modal fade" id="retire" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"></div></div>

    <div class="modal fade" id="settingswallet" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"></div></div>

    <div class="modal fade" id="details" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"></div></div>

    <div class="modal fade" id="awards" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"></div></div>

    <button type="button" class="btn btn-primary btn-lg bingo" onclick="singBingo();">BINGO</button>

    <div id="countdown-container" style="position: fixed; display: none;">
        <div class="countdown-container"> 
            <div id="countdown">10</div>
        </div>
    </div>

    <div id="game-finalized" style="position: fixed; display: none;">
        <div class="game-finalized"> 
            <div id="finalized"></div>
        </div>
    </div>

    <div id="whatsapp-plugin"></div>
</div>

<script src="<?= site_url('assets/plugin/components/moment/moment.min.js'); ?>?<?= md5(date("Hms")); ?>"></script>
<script src="<?= site_url('assets/plugin/components/moment/moment-timezone-with-data.min.js'); ?>?<?= md5(date("Hms")); ?>"></script>
<script src="<?= site_url('assets/plugin/czm-chat-support.min.js'); ?>?<?= md5(date("Hms")); ?>"></script>

<script type="text/javascript">
    function awardsGet() {
        $("#awards").load('<?= site_url('playings/awardsGet') ?>');
        $('#awards').modal('show');
        $('#game-finalized').hide();
    }

    function paymentsGet() {
        $("#payments").load('<?= site_url('payments/paymentsGet') ?>');
        $('#payments').modal('show');
    }

    function rechargeGet() {
        $("#recharge").load('<?= site_url('payments/rechargeGet') ?>');
        $('#recharge').modal('show');
    }

    function retireGet() {
        $("#retire").load('<?= site_url('payments/retireGet') ?>');
        $('#retire').modal('show');
    }

    function settingswalletGet() {
        $("#settingswallet").load('<?= site_url('payments/settingswalletGet') ?>');
        $('#settingswallet').modal('show');
    }

    function paydetailsGet() {
        $("#details").load('<?= site_url('payments/paydetailsGet') ?>');
        $('#details').modal('show');
    }

    const container = document.querySelector('.container-cartons');
    const cartons = document.querySelectorAll('.bingo-carton');

    if (cartons.length == 1) {
        container.classList.add('single-carton');
    } else if (cartons.length >= 2 && cartons.length <= 4) {
        container.classList.add('more-cartons');
    } else if (cartons.length == 5) {
        container.classList.remove('more-cartons');
        container.classList.add('five-cartons');
    } else if (cartons.length >= 6) {
        container.classList.add('five-cartons');
        container.classList.add('six-cartons');
    }

    let lastMessageIntervalGet;
    let messagesDisplayed = [];
    
    function GoToPage(page) {
        $.ajax({
            url: '<?= site_url(); ?>' + page,
            success: function (data) {
                if (page != 'logout') {
                    $('#content-page').html(data);
                } else {
                    window.location.href = page;
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const musicButton = document.querySelector('.controls-bingo.music');
        const controlsContainer = document.querySelector('.controls-container');

        musicButton.addEventListener('click', function() {
            controlsContainer.classList.toggle('active');
        });
    });

    document.getElementById("toggle-messages-btn").addEventListener("click", function(event) { 
        const messageContainer = document.getElementById("message-display-container");

        if (messageContainer.style.display === "none" || messageContainer.style.display === "") {
            messageContainer.style.display = "flex";
        } else {
            messageContainer.style.display = "none";
        }

        event.stopPropagation();
    });

    document.addEventListener("click", function(event) {
        const messageContainer = document.getElementById("message-display-container");
        const toggleButton = document.getElementById("toggle-messages-btn");

        if (messageContainer.style.display === "flex" && 
            !messageContainer.contains(event.target) && 
            !toggleButton.contains(event.target)) {
            messageContainer.style.display = "none";
        }
    });

    function sendEmoji(emoji, id) {
        var messageDisplay = document.getElementById("message-display");

        limitMessages(messageDisplay);

        var newEmojiBubble = createMessageBubble(emoji, '<?= $imagePath ?>');

        $.ajax({
            url: '<?= site_url('playings/messageSubmit') ?>',
            method: 'POST',
            data: {
                message: emoji
            },
            success: function(data) {
                if (data.status === 'success') {
                    messageDisplay.appendChild(newEmojiBubble);
                    scrollToBottom(); 

                    $('#message-send-new').val('');

                    messagesDisplayed.push(id);
                } else {
                    console.log("error sending message");
                }
            },
            error: function(error) {
                console.log("request error while sending message");
            }
        });

        setTimeout(function() {
            removeMessageWithFade(newEmojiBubble);
        }, 3000);
    }

    function sendMessage(message, id) {
        var messageDisplay = document.getElementById("message-display");

        limitMessages(messageDisplay);

        var newMessageBubble = createMessageBubble(message, '<?= $imagePath ?>');

        $.ajax({
            url: '<?= site_url('playings/messageSubmit') ?>',
            method: 'POST',
            data: {
                message: message
            },
            success: function(data) {
                if (data.status === 'success') {
                    messageDisplay.appendChild(newMessageBubble);
                    scrollToBottom();

                    messagesDisplayed.push(id);
                } else {
                    console.log("error sending message: " + data.message);
                }
            },
            error: function(error) {
                console.log("request error while sending message:", error);
            }
        });

        setTimeout(function() {
            removeMessageWithFade(newMessageBubble);
        }, 3000);
    }

    $(document).ready(function() {
        function sendMessageText() {
            var messageDisplay = document.getElementById("message-display");
            var message = $('#message-send-new').val();

            if (message.trim() === '') {
                return;
            }

            limitMessages(messageDisplay);

            var newMessageBubble = createMessageBubble(message, '<?= $imagePath ?>');

            $.ajax({
                url: '<?= site_url('playings/messageSubmit') ?>',
                method: 'POST',
                data: {
                    message: message
                },
                success: function(data) {
                    if (data.status === 'success') {
                        messageDisplay.appendChild(newMessageBubble);
                        scrollToBottom(); 
                        $('#message-send-new').val('');

                        messagesDisplayed.push(data.id);
                    } else {
                        console.log("error sending message: " + data.message);
                    }
                },
                error: function(error) {
                    console.log("request error while sending message:", error);
                }
            });

            setTimeout(function() {
                removeMessageWithFade(newMessageBubble);
            }, 3000);
        }

        $(document).on('keypress', '#message-send-new', function(e) {
            if (e.which === 13) { 
                sendMessageText();
            }
        });

        $(document).on('click', '#message-button', function() {
            sendMessageText();
        });
    });

    function lastMessageGet() {
        $.ajax({
            url: '<?= site_url('playings/messageGet') ?>',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                if (data.status === 'stop') {
                    stoplastMessageGet();
                    return;
                }

                if (data.status === 'success') {
                    let lastMessage = data.message;

                    if (messagesDisplayed.includes(lastMessage.id)) {
                        return; 
                    }

                    var newMessageBubble = createMessageBubble(
                        lastMessage.message,
                        data.image
                    );

                    document.getElementById("message-display").appendChild(newMessageBubble);

                    messagesDisplayed.push(lastMessage.id);

                    scrollToBottom();

                    setTimeout(function () {
                        removeMessageWithFade(newMessageBubble);
                    }, 3000);
                } else {
                    console.log(data.message);
                }
            },
            error: function (error) {
                console.error('request error:', error);
            }
        });
    }

    function stoplastMessageGet() {
        if (lastMessageIntervalGet) {
            clearInterval(lastMessageIntervalGet);
            console.log("getting messages has stopped automatically");
        }
    }

    $(document).ready(function () {
        lastMessageIntervalGet = setInterval(lastMessageGet, 500);
        lastMessageGet();
    });

    function createMessageBubble(content, profilePicUrl) {
        var messageBubble = document.createElement("div");
        messageBubble.classList.add("message-bubble");

        var profilePic = document.createElement("img");
        profilePic.classList.add("profile-pic");
        profilePic.src = profilePicUrl;

        var messageContent = document.createElement("span");
        messageContent.textContent = content;

        if (/[\u1F600-\u1F6FF]/.test(content)) {
            messageContent.classList.add("text-message");
        } else {
            messageContent.classList.add("emoji-message");
        }

        messageBubble.appendChild(profilePic);
        messageBubble.appendChild(messageContent);

        return messageBubble;
    }

    function removeMessageWithFade(messageElement) {
        messageElement.classList.add("fade-out");

        setTimeout(function() {
            messageElement.remove();
        }, 500);
    }

    function removeOldMessages() {
        var messageDisplay = document.getElementById("message-display");
        var messages = messageDisplay.getElementsByClassName("message-bubble");

        if (messages.length > 5) {
            var oldestMessage = messages[0];

            oldestMessage.classList.add("fade-out");

            setTimeout(function() {
                oldestMessage.remove();
            }, 500);
        }
    }

    function limitMessages(messageDisplay) {
        var messageBubbles = messageDisplay.getElementsByClassName("message-bubble");

        if (messageBubbles.length >= 5) {
            removeMessageWithFade(messageBubbles[0]);
        }
    }

    function scrollToBottom() {
        var messageDisplay = document.getElementById("message-display");
        messageDisplay.scrollTop = messageDisplay.scrollHeight;
    }

    const emojiSlider = document.querySelector('.emoji-slider');
    emojiSlider.addEventListener('wheel', (e) => {
        e.preventDefault(); 
        emojiSlider.scrollLeft += e.deltaY;
    });

    const messageSlider = document.querySelector('.message-bubble-slider');
    messageSlider.addEventListener('wheel', (e) => {
        e.preventDefault();
        messageSlider.scrollLeft += e.deltaY;
    });

    let numbersgenerated = []; 
    let lastNumbers = []; 
    let generationInterval; 
    let isGameFinishedShown = false; 
    let narrationAudio;
    let narrationPlaying = <?php if ($user['narration'] == 1): ?>true<?php else : ?>false<?php endif; ?>; 
    let autoMarkEnabled = <?php if ($user['autodial'] == 1): ?>true<?php else : ?>false<?php endif; ?>; 

    function lastNumberGet() {
        $.ajax({
            url: '<?= site_url('playings/numberGet') ?>',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.status === 'pause') {
                    clearInterval(generationInterval);
                    console.log("automatic generation stopped");
                    let countdownNumberHe = document.getElementById('countdown');
                    let countdownContainer = document.getElementById('countdown-container');
                    let countdown = 5;

                    let userImagePath = data.image; 
                    console.log(userImagePath); 

                    countdownContainer.style.display = 'block';

                    countdownNumberHe.textContent = '¡Bingo!';
                    countdownNumberHe.style.color = 'white'; 

                    setTimeout(() => {
                        countdownNumberHe.style.backgroundImage = `url(${userImagePath})`;
                        countdownNumberHe.style.backgroundSize = 'cover';
                        countdownNumberHe.style.backgroundPosition = 'center';
                        countdownNumberHe.style.color = 'transparent'; 

                        setTimeout(() => {
                            countdownNumberHe.style.backgroundImage = '';
                            countdownNumberHe.style.background = 'linear-gradient(145deg, #FF66C4, #ff9800)';
                            countdownNumberHe.style.color = 'white'; 
                            countdownNumberHe.textContent = countdown;

                            let countdownInterval = setInterval(() => {
                                countdown--;
                                countdownNumberHe.textContent = countdown;

                                if (countdown === 0) {
                                    clearInterval(countdownInterval);
                                    countdownContainer.style.display = 'none'; 
                                    startAutomaticGeneration(); 
                                }
                            }, 1000);
                        }, 3000);

                    }, 2000);

                    createConfetti();
                } else {
                    if (data.status === 'success') {
                        let lastNumber = data.number;

                        if (numbersgenerated.includes(lastNumber)) {
                            return; 
                        }

                        numbersgenerated.push(lastNumber);

                        lastNumbers.push(lastNumber);

                        if (lastNumbers.length > 5) {
                            lastNumbers.shift();
                        }

                        $("#last-numbers").html("");

                        lastNumbers.forEach(function(number) {
                            let classNumber = getClassByRange(number);
                            $("#last-numbers").append('<div class="ball-number ball-60 ' + classNumber + '"><span>' + number + '</span></div>');
                        });

                        $("#generated-number").html('<span>' + lastNumber + '</span>').addClass("move-number");

                        if (narrationPlaying) {
                            if (narrationAudio) {
                                narrationAudio.pause();
                                narrationAudio.currentTime = 0;
                            }
                            narrationAudio = new Audio('<?= site_url('assets/sounds/') ?>' + lastNumber + '.mp3');
                            narrationAudio.play();
                        }

                        setTimeout(function () {
                            $("#generated-number").removeClass("move-number");
                        }, 1000);

                        if (autoMarkEnabled) {
                            dialNumber(lastNumber);
                        }

                        if (lastNumber <= 15) {
                            $("#board-number-" + lastNumber).addClass('b-col');
                        } else if (lastNumber <= 30) {
                            $("#board-number-" + lastNumber).addClass('i-col');
                        } else if (lastNumber <= 45) {
                            $("#board-number-" + lastNumber).addClass('n-col');
                        } else if (lastNumber <= 60) {
                            $("#board-number-" + lastNumber).addClass('g-col');
                        } else if (lastNumber <= 75) {
                            $("#board-number-" + lastNumber).addClass('o-col');
                        }
                    }

                    if (data.status === 'completed') {
                        let lastNumber = data.number;

                        if (numbersgenerated.includes(lastNumber)) {
                            return; 
                        }

                        numbersgenerated.push(lastNumber);

                        lastNumbers.push(lastNumber);

                        if (lastNumbers.length > 5) {
                            lastNumbers.shift();
                        }

                        $("#last-numbers").html("");

                        lastNumbers.forEach(function(number) {
                            let classNumber = getClassByRange(number);
                            $("#last-numbers").append('<div class="ball-number ball-60 ' + classNumber + '"><span>' + number + '</span></div>');
                        });

                        $("#generated-number").html('<span>' + lastNumber + '</span>').addClass("move-number");

                        if (narrationPlaying) {
                            if (narrationAudio) {
                                narrationAudio.pause();
                                narrationAudio.currentTime = 0;
                            }
                            narrationAudio = new Audio('<?= site_url('assets/sounds/') ?>' + lastNumber + '.mp3');
                            narrationAudio.play();
                        }

                        setTimeout(function () {
                            $("#generated-number").removeClass("move-number");
                        }, 1000);

                        if (autoMarkEnabled) {
                            dialNumber(lastNumber);
                        }

                        if (lastNumber <= 15) {
                            $("#board-number-" + lastNumber).addClass('b-col');
                        } else if (lastNumber <= 30) {
                            $("#board-number-" + lastNumber).addClass('i-col');
                        } else if (lastNumber <= 45) {
                            $("#board-number-" + lastNumber).addClass('n-col');
                        } else if (lastNumber <= 60) {
                            $("#board-number-" + lastNumber).addClass('g-col');
                        } else if (lastNumber <= 75) {
                            $("#board-number-" + lastNumber).addClass('o-col');
                        }

                        stopAutomaticGeneration();
                        setTimeout(() => {
                            showGamefinalized();
                        }, 3000); 
                    }
                }
            }
        });
    }

    function dialNumber(number) {
        let elementsNumber = $(".number-" + number);
        if (elementsNumber.length) {
            $.ajax({
                url: '<?= site_url('playings/dialNumber') ?>',
                method: 'POST',
                data: {
                    number: number
                },
                success: function(data) {
                    if (data.status === 'success') {
                        console.log("the number " + number + " has been marked correctly in the database");

                        elementsNumber.each(function() {
                            let elementNumber = $(this);

                            const originalContent = elementNumber.text();

                            elementNumber.text('🌟').addClass('explosive-effect');

                            setTimeout(function() {
                                elementNumber.text(originalContent); 
                                elementNumber.removeClass('explosive-effect');
                                elementNumber.addClass('marked'); 
                            }, 1000); 
                        });
                    } else {
                        console.log("error when dialing the number: " + data.message);
                        elementsNumber.removeClass('marked');
                    }
                },
                error: function(error) {
                    console.log("error in the request when dialing the number:", error);
                    elementsNumber.removeClass('marked');
                }
            });
        }
    }

    $(".card-number").click(function() {
        if (!autoMarkEnabled) {
            let number = $(this).data('number'); 
            dialNumber(number);  
        }
    });

    function getClassByRange(number) {
        if (number <= 15) {
            return 'b-col';
        } else if (number <= 30) {
            return 'i-col';
        } else if (number <= 45) {
            return 'n-col';
        } else if (number <= 60) {
            return 'g-col';
        } else if (number <= 75) {
            return 'o-col';
        }
    }

    $(document).ready(function () {
        startAutomaticGeneration();
    });

    function startAutomaticGeneration() {
        clearInterval(generationInterval);
        generationInterval = setInterval(lastNumberGet, 2500);
    }

    function stopAutomaticGeneration() {
        clearInterval(generationInterval);
        console.log("automatic generation stopped");
    }

    function singBingo() {
        $.ajax({
            url: '<?= site_url('playings/singBingo') ?>',
            method: 'POST',
            success: function(data) {
                if (data.status === 'success') {

                        sendMessage('<?= translate('bingo!'); ?> 🥳', 22);

                        let countdownNumberHe = document.getElementById('countdown');
                        let countdownContainer = document.getElementById('countdown-container');
                        let countdown = 5;

                        let userImagePath = data.image; 
                        console.log(userImagePath);

                        countdownContainer.style.display = 'block';

                        countdownNumberHe.textContent = '<?= translate('bingo!'); ?>';
                        countdownNumberHe.style.color = 'white'; 

                        setTimeout(() => {
                            countdownNumberHe.style.backgroundImage = `url(${userImagePath})`;
                            countdownNumberHe.style.backgroundSize = 'cover';
                            countdownNumberHe.style.backgroundPosition = 'center';
                            countdownNumberHe.style.color = 'transparent';

                            setTimeout(() => {
                                countdownNumberHe.style.backgroundImage = '';
                                countdownNumberHe.style.background = 'linear-gradient(145deg, #FF66C4, #ff9800)';
                                countdownNumberHe.style.color = 'white'; 
                                countdownNumberHe.textContent = countdown; 

                                let countdownInterval = setInterval(() => {
                                    countdown--;
                                    countdownNumberHe.textContent = countdown;

                                    if (countdown === 0) {
                                        clearInterval(countdownInterval);
                                        countdownContainer.style.display = 'none'; 
                                        startAutomaticGeneration(); 
                                    }
                                }, 1000);
                            }, 3000); 

                        }, 2000); 

                        createConfetti();

                        /*const confettiContainer = document.getElementById('confetti-container');

                        for (let i = 0; i < 100; i++) {
                            const confettiPiece = document.createElement('div');
                            confettiPiece.classList.add('confetti-uno');
                            
                            // Establecer posición inicial aleatoria
                            confettiPiece.style.left = Math.random() * 100 + 'vw';
                            confettiPiece.style.animationDuration = Math.random() * 3 + 2 + 's'; // Entre 2 y 5 segundos
                            confettiPiece.style.setProperty('--color', getRandomColor());
                            
                            // Añadir la pieza al contenedor
                            confettiContainer.appendChild(confettiPiece);

                            // Eliminar después de que termine la animación
                            setTimeout(() => {
                                confettiPiece.remove();
                            }, 5000);
                        }

                        // Función para generar colores aleatorios
                        function getRandomColor() {
                            const colors = ['#ff0', '#f0f', '#0ff', '#0f0', '#f00', '#00f'];
                            return colors[Math.floor(Math.random() * colors.length)];
                        }*/
                    /*} else {
                        const confettiContainer = document.getElementById('confetti-container');

                        // Limpiar cualquier confeti anterior
                        burstContainer.innerHTML = '';

                        for (let i = 0; i < 50; i++) {
                            const confettiPiece = document.createElement('div');
                            confettiPiece.classList.add('confetti-dos');

                            // Generar posición y dirección aleatoria
                            const x = (Math.random() - 0.5) * 200 + 'px';  // Valor aleatorio entre -100px y 100px
                            const y = (Math.random() - 0.5) * 200 + 'px';  // Valor aleatorio entre -100px y 100px
                            confettiPiece.style.setProperty('--x', x);
                            confettiPiece.style.setProperty('--y', y);
                            confettiPiece.style.animationDuration = Math.random() * 0.5 + 0.5 + 's'; // Entre 0.5 y 1 segundo
                            confettiPiece.style.setProperty('--color', getRandomColor());

                            // Añadir la pieza al contenedor
                            burstContainer.appendChild(confettiPiece);

                            // Eliminar después de que termine la animación
                            setTimeout(() => {
                                confettiPiece.remove();
                            }, 1000);
                        }

                        // Función para generar colores aleatorios
                        function getRandomColor() {
                            const colors = ['#ff0', '#f0f', '#0ff', '#0f0', '#f00', '#00f'];
                            return colors[Math.floor(Math.random() * colors.length)];
                        }
                    }*/
                } else {
                    console.log(data.message);
                }
            }
        });
    }

    function showGamefinalized() {
        if (isGameFinishedShown) {
            return;
        }

        let countdownNumberHe = document.getElementById('finalized'); 
        let countdownContainer = document.getElementById('game-finalized');

        countdownContainer.style.display = 'block';
        countdownNumberHe.innerHTML = '<?= translate('game finished!'); ?>';

        setTimeout(function() {
            awardsGet(); 
            countdownContainer.style.display = 'none'; 
        }, 5000); 

        isGameFinishedShown = true;
    }

    function createConfetti() {
        const emojis = ['🎉', '🎊', '✨', '🌟', '🥳', '🍾', '💥', '🔥', '💫', '🍬', '🎈'];
        const confettiCount = 150; 

        for (let i = 0; i < confettiCount; i++) {
            const confetti = $('<div class="confetti"></div>');

            confetti.text(emojis[Math.floor(Math.random() * emojis.length)]);

            confetti.css({
                left: Math.random() * 150 + 'vw',
                top: Math.random() * -100 + 'vh',
                fontSize: (Math.random() * 30 + 10) + 'px', 
                animationDuration: (Math.random() * 5 + 1) + 's',
                animationDelay: Math.random() + 's'
            });

            $('body').append(confetti);

            confetti.on('animationend', function() {
                $(this).remove();
            });
        }
    }

    function RemoveVolume() {
        $.ajax({
            url: '<?= site_url('playings/volumeSubmit') ?>',
            method: 'POST',
            success: function(data) {
                if (data.status === 'success') {
                    console.log("sound disabled successfully");
                } else {
                    console.log("error sending request");
                }
            },
            error: function(error) {
                console.log("error in the request");
            }
        });
    }

    function RemoveMicrophone() {
        $.ajax({
            url: '<?= site_url('playings/microphoneSubmit') ?>',
            method: 'POST',
            success: function(data) {
                if (data.status === 'success') {
                    console.log("narrator disabled successfully");
                } else {
                    console.log("error sending request");
                }
            },
            error: function(error) {
                console.log("error in the request");
            }
        });
    }

    function RemoveCheck() {
        $.ajax({
            url: '<?= site_url('playings/checkSubmit') ?>',
            method: 'POST',
            success: function(data) {
                if (data.status === 'success') {
                    console.log("automarked disabled successfully");
                } else {
                    console.log("error sending request");
                }
            },
            error: function(error) {
                console.log("error in the request");
            }
        });
    }

    $('.microphone').click(function() {
        narrationPlaying = !narrationPlaying;
        $(this).html(narrationPlaying ? '<i class="fa-duotone fa-solid fa-microphone"></i>' : '<i class="fa-duotone fa-solid fa-microphone-slash"></i>');
    });

    $('.check').click(function() {
        autoMarkEnabled = !autoMarkEnabled;
        $(this).html(autoMarkEnabled ? '<i class="fa-duotone fa-solid fa-eye"></i>' : '<i class="fa-duotone fa-solid fa-eye-slash"></i>');
    });

    $('#whatsapp-plugin').czmChatSupport({
        /* Button Settings */
        button: {
            position: "right", /* left, right or false. "position:false" does not pin to the left or right */
            style: 1, /* Button style. Number between 1 and 7 */
            src: '<i class="fab fa-whatsapp"></i>', /* Image, Icon or SVG */
            backgroundColor: "#10c379", /* Html color code */
            effect: 1, /* Button effect. Number between 1 and 7 */
            notificationNumber: "1", /* Custom text or false. To remove, (notificationNumber:false) */
            speechBubble: "<?= translate('how can we help you?'); ?>", /* To remove, (speechBubble:false) */
            pulseEffect: true, /* To remove, (pulseEffect:false) */
            text: { /* For Button style larger than 1 */
                title: "<?= translate('do you need help? talk to us'); ?>", /* Writing is required */
                description: "<?= translate('customer service'); ?>", /* To remove, (description:false) */
                online: "<?= translate('online'); ?>", /* To remove, (online:false) */
                offline: "<?= translate('offline'); ?>" /* To remove, (offline:false) */
            }
        },
    
        /* Popup Settings */
        popup: {
            automaticOpen: false, /* true or false (Open popup automatically when the page is loaded) */
            outsideClickClosePopup: true, /* true or false (Clicking anywhere on the page will close the popup) */
            effect: 1, /* Popup opening effect. Number between 1 and 15 */
            header: {
                backgroundColor: "#10c379", /* Html color code */
                title: "<?= translate('do you need help? talk to us'); ?>", /* Writing is required */
                description: "<?= translate('one of our representatives will assist you'); ?>" /* To remove, (description:false) */
            },
    
            /* Representative Settings */
            persons: [

                <?php foreach ($contacts as $contact): ?>
                {
                    avatar: {
                        src: '<img src="<?= site_url('assets/img/person/' . $contact['id'] . '.svg'); ?>" alt="img">', /* Image, Icon or SVG */
                        backgroundColor: "#ffffff", /* Html color code */
                        onlineCircle: true /* Avatar online circle. To remove, (onlineCircle:false) */
                    },
                    text: {
                        title: "<?= $contact['name'] ?>", /* Writing is required */
                        description: "<?= $contact['charge'] ?>", /* To remove, (description:false) */
                        online: "<?= translate('online'); ?>", /* To remove, (online:false) */
                        offline: "<?= translate('offline'); ?>", /* To remove, (offline:false) */
                        button: "<?= translate('start chat'); ?>"
                    },
                    link: {
                        desktop: "https://web.whatsapp.com/send?phone=<?= $contact['phone'] ?>&text=<?= translate('hello'); ?> <?= $contact['name'] ?>, <?= translate('i need more information'); ?>.", /* Writing is required */
                        mobile: "https://wa.me/<?= $contact['phone'] ?>/?text=<?= translate('hello'); ?> <?= $contact['name'] ?>, <?= translate('i need more information'); ?>." /* If it is hidden desktop link will be valid. To remove, (mobile:false) */
                    },
                    onlineDay: {
                        /* Change the day you are offline like this. (sunday:false) */
                        sunday: "00:00-23:59",
                        monday: "00:00-23:59",
                        tuesday: "00:00-23:59",
                        wednesday: "00:00-23:59",
                        thursday: "00:00-23:59",
                        friday: "00:00-23:59",
                        saturday: "00:00-23:59"
                    }
                },
                <?php endforeach; ?>

            ],
        },
    
        /* Other Settings */
        sound: true, /* true (default sound), false or custom sound. Custom sound example, (sound:'assets/sound/notification.mp3') */
        changeBrowserTitle: false, /* Custom text or false. To remove, (changeBrowserTitle:false) */
        cookie: false, /* It does not show the speech bubble, notification number, and pulse effect again for the specified time. For example, do not show for 1 hour, (cookie:1) or to remove, (cookie:false) */
    });
</script>