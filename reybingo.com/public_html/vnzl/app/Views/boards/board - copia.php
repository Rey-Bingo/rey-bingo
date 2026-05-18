<style>
    .column {
        display: grid;
        grid-template-columns: repeat(16, 1fr);
        gap: 5px;
        text-align: center;
    }

    #last-numbers {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 5px;
        text-align: center;
        position: fixed;
        bottom: 13%;
    }

    .letter, .numero {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.5), inset 0 -5px 10px rgba(0, 0, 0, 0.2);
        position: relative;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        color: #ffffff;
        margin-bottom: 5px;
    }

    /*.letter, .numero:hover {
        transform: scale(1.1);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.6), inset 0 -8px 15px rgba(0, 0, 0, 0.3);
    }*/

    .numero {
        background-color: #ffffff;
        border: 5px solid #909090;
    }

    .numero::before {
        content: '';
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        width: 26px;
        height: 26px;
        border: 2px solid #909090;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.5)  0%, rgba(255, 255, 255, 0) 70%);
    }

    .letter span, .numero span {
        color: #535560;
        font-weight: bold;
        width: 23px;
        height: 23px;
        font-size: .8rem;
        transform: scale(1.1);
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        border-radius: 50%;
    }

    .letter::before {
        content: '';
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.5)  0%, rgba(255, 255, 255, 0) 70%);
    }

    .highlighted {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transform: scale(1.3);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4), inset 0 0 10px rgba(255, 255, 255, 0.7);
        position: relative;
        color: #ffffff;
        margin-bottom: 5px;
    }

    .marked {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.5), inset 0 -5px 10px rgba(0, 0, 0, 0.2);
        position: relative;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        color: #ffffff;
        margin-bottom: 5px;
    }

    .highlighted span, .marked span {
        color: #535560;
        font-weight: bold;
        width: 23px;
        height: 23px;
        font-size: 0.8rem;
        transform: scale(1.1);
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        border-radius: 50%;
    }

    .highlighted::before, .marked::before {
        content: '';
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        width: 26px;
        height: 26px;
        border: 2px solid #535560;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.5)  0%, rgba(255, 255, 255, 0) 70%);
    }

    .b-col {
        background-color: #ffffff;
        border: 5px solid #fa5c93;
    }

    .i-col {
        background-color: #ffffff;
        border: 5px solid #f9ac0a;
    }

    .n-col {
        background-color: #ffffff;
        border: 5px solid #fbd60d;
    }

    .g-col {
        background-color: #ffffff;
        border: 5px solid #00ba71;
    }

    .o-col {
        background-color: #ffffff;
        border: 5px solid #22cbe6;
    }

    .b-col::before {
        border: 2px solid #fa5c93;
    }

    .i-col::before {
        border: 2px solid #f9ac0a;
    }

    .n-col::before {
        border: 2px solid #fbd60d;
    }

    .g-col::before {
        border: 2px solid #00ba71;
    }

    .o-col::before {
        border: 2px solid #22cbe6;
    }

    @keyframes moveBall {
        0% {
            transform: translateY(-1000px) scale(0.5);
            opacity: 0;
        }
        50% {
            transform: translateY(0) scale(1.2);
            opacity: 1;
        }
        100% {
            transform: translateY(0) scale(1);
        }
    }

    #generated-number {
        bottom: 65px;
        right: 10px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4), inset 0 0 15px rgba(255, 255, 255, 0.7);
        animation: moveBall 1s ease-out;
        position: fixed;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    /*#number-generado span {
        color: #535560;
        font-weight: bold;
        z-index: 2;
        width: 80px;
        height: 80px;
        transform: scale(1.1);
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        border-radius: 50%;
    }

    #number-generado::before {
        content: '';
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.5)  0%, rgba(255, 255, 255, 0) 70%);
        z-index: 1;
    }*/

    .last-number {
        font-size: 1.2rem;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4), inset 0 0 15px rgba(255, 255, 255, 0.7);
        animation: moveBall 1s ease-out;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin-bottom: 0px;
    }

    .last-number span {
        color: #535560;
        font-weight: bold;
        width: 45px;
        height: 45px;
        transform: scale(1.1);
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        border-radius: 50%;
    }

    .last-number::before {
        content: '';
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        width: 37px;
        height: 37px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.5)  0%, rgba(255, 255, 255, 0) 70%);
    }

    .move-number {
        animation: moveBallTowards .5s ease-out forwards;
    }

    @keyframes moveBallTowards {
        0% {
            transform: translateY(-50px) scale(1.5);
            opacity: 0;
        }
        100% {
            transform: translateY(0) scale(1);
            opacity: 1;
        }
    }

    /* .col-b {
        background-color: #ffffff;
        border: 7px solid #fa5c93;
    }
    .col-i {
        background-color: #ffffff;
        border: 7px solid #f9ac0a;
    }
    .col-n {
        background-color: #ffffff;
        border: 7px solid #fbd60d;
    }
    .col-g {
        background-color: #ffffff;
        border: 7px solid #00ba71;
    }
    .col-o {
        background-color: #ffffff;
        border: 7px solid #22cbe6;
    }

    .col-b::before {
        border: 3.5px solid #fa5c93;
    }
    .col-i::before {
        border: 3.5px solid #f9ac0a;
    }
    .col-n::before {
        border: 3.5px solid #fbd60d;
    }
    .col-g::before {
        border: 3.5px solid #00ba71;
    }
    .col-o::before {
        border: 3.5px solid #22cbe6;
    }*/
    
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

    .ball-40 {
        width: 40px;
        height: 40px;
        border: 5px solid #909090;
    }

    .ball-60 {
        width: 60px;
        height: 60px;
        border: 7px solid #909090;
    }

    .ball-90 {
        width: 90px;
        height: 90px;
        border: 9px solid #909090;
    }

    .ball-number span {
        font-weight: bold;
        color: #535560;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        position: absolute;
    }

    .ball-40 span {
        width: 23px;
        height: 23px;
        font-size: 0.8rem;
    }

    .ball-60 span {
        width: 35px;
        height: 35px;
        font-size: 1.2rem;
    }

    .ball-90 span {
        width: 55px;
        height: 55px;
        font-size: 1.6rem;
    }

    .ball-number::before {
        content: '';
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.5)  0%, rgba(255, 255, 255, 0) 70%);
    }

    .ball-40::before {
        width: 26px;
        height: 26px;
        border: 2px solid #909090;
    }

    .ball-60::before {
        width: 40px;
        height: 40px;
        border: 3px solid #909090;
    }

    .ball-90::before {
        width: 65px;
        height: 65px;
        border: 4px solid #909090;
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

    @media (max-width: 768px) {
        .board-number {
            display: flex;
            justify-content: center;
            gap: 5px;
            max-width: 90%;
            margin: 0 auto;
            position: relative;
            top: -20px;
        }

        #last-numbers {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            position: fixed;
            right: 10px;
            bottom: 20%;
        }

        .column {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0px;
        }

        .ball-40 {
            width: 30px;
            height: 30px;
            border: 4px solid #909090;
        }

        .ball-40 span {
            width: 18px;
            height: 18px;
            font-size: 0.6rem;
        }

        .ball-40::before {
            width: 20px;
            height: 20px;
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

        .highlighted, .marked {
            width: 30px;
            height: 30px;
            border: 4px solid #909090;
        }

        .highlighted span, .marked span {
            width: 18px;
            height: 18px;
            font-size: 0.6rem;
        }

        .highlighted::before, .marked::before {
            width: 20px;
            height: 20px;
            border: 1px solid #535560;
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
    }

    .confetti {
        position: fixed;
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
            transform: translateY(-100vh) rotate(0deg); 
        }
        100% {
            transform: translateY(100vh) rotate(720deg);
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
        z-index: 9999; 
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

    .count_notifications {
        position: absolute;
        left: 100%;
        transform: translate(-70%, -80%);
        background-color: red;
        color: white;
        font-size: .7rem;
        padding: 1px 1px;
        border-radius: 50%;
        min-width: 17px;
        text-align: center;
        line-height: 1.4;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>

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

        <button type="button" class="btn btn-primary controls-bingo check" id="toggle-marcar-button">
            <i class="fa-duotone fa-solid fa-check"></i>
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

    <button type="button" class="btn btn-primary controls-bingo player" onclick="playersGet();">
        <i class="fa-duotone fa-solid fa-users-line"></i>
        <span class="count_notifications"></span>
    </button>

    <button type="button" class="btn btn-primary controls-bingo award-board" onclick="awardsGet();">
        <i class="fa-duotone fa-solid fa-award"></i>
    </button>

    <button type="button" class="btn btn-primary controls-bingo wallet-board" onclick="paymentsGet();">
        <i class="fa-duotone fa-solid fa-envelope-open-dollar"></i>
    </button>

    <button type="button" class="btn btn-primary controls-bingo game-board" onclick="gamesGet();">
        <i class="fa-duotone fa-solid fa-gamepad"></i>
    </button>

    <h1 class="fs-5 text-center" style="padding-top: 60px;"><?= $game['description']; ?></h1>
</div>

<div class="container-board">
    <div class="board-number">
        <div class="column">
            <div class="ball-number ball-40 b-col"><span>B</span></div>
            <?php foreach (range(1, 15) as $number): ?>
                <?php $class = in_array($number, $selectedNumbers) ? $getClass($number) : ''; ?>
                <div class="ball-number ball-40 <?= $class ?>" id="number-<?= $number ?>">
                    <span><?= $number ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="column">
            <div class="ball-number ball-40 i-col"><span>I</span></div>
            <?php foreach (range(16, 30) as $number): ?>
                <?php $class = in_array($number, $selectedNumbers) ? $getClass($number) : ''; ?>
                <div class="ball-number ball-40 <?= $class ?>" id="number-<?= $number ?>">
                    <span><?= $number ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="column">
            <div class="ball-number ball-40 n-col"><span>N</span></div>
            <?php foreach (range(31, 45) as $number): ?>
                <?php $class = in_array($number, $selectedNumbers) ? $getClass($number) : ''; ?>
                <div class="ball-number ball-40 <?= $class ?>" id="number-<?= $number ?>">
                    <span><?= $number ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="column">
            <div class="ball-number ball-40 g-col"><span>G</span></div>
            <?php foreach (range(46, 60) as $number): ?>
                <?php $class = in_array($number, $selectedNumbers) ? $getClass($number) : ''; ?>
                <div class="ball-number ball-40 <?= $class ?>" id="number-<?= $number ?>">
                    <span><?= $number ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="column">
            <div class="ball-number ball-40 o-col"><span>O</span></div>
            <?php foreach (range(61, 75) as $number): ?>
                <?php $class = in_array($number, $selectedNumbers) ? $getClass($number) : ''; ?>
                <div class="ball-number ball-40 <?= $class ?>" id="number-<?= $number ?>">
                    <span><?= $number ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="bottom">

    <button type="button" class="btn btn-primary controls-bingo gear" onclick="settingsGet();">
        <i class="fa-duotone fa-solid fa-gear"></i>
    </button>

    <?php if ($status != 'stop') : ?>
    <div id="controls">
        <button id="start-button" class="btn btn-primary control-play"><i class="fa-duotone fa-solid fa-play"></i></button>
        <button id="play-button" class="btn btn-primary control-play" style="display: none;"><i class="fa-duotone fa-solid fa-play"></i></button>
        <button id="stop-button" class="btn btn-primary control-pause" style="display: none;"><i class="fa-duotone fa-solid fa-pause"></i></button>
        <button id="next-number-button" class="btn btn-primary control-step" style="display: none;"><i class="fa-duotone fa-solid fa-forward-step"></i></button>
    </div>
    <?php endif; ?>

    <div id="last-numbers"></div>

    <div id="generated-number" class="ball-number ball-90" style="display: none;"><span></span></div>

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
                <button type="button" class="message-btn" onclick="sendMessage('<?= translate('ha ha ha'); ?> 🤣', 23)"><?= translate('ha ha ha'); ?> 🤣</button>
                <button type="button" class="message-btn" onclick="sendMessage('<?= translate('good luck!'); ?> 🤑', 24)"><?= translate('good luck!'); ?> 🤑</button>
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

    <div class="modal fade" id="payments" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"></div></div>

    <div class="modal fade" id="games" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"></div></div>

    <div class="modal fade" id="awards" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"></div></div>

    <div class="modal fade" id="players" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"></div></div>

    <div class="modal fade" id="settings" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"></div></div>

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
</div>

<script type="text/javascript">
    function awardsGet() {
        $("#awards").load('<?= site_url('boards/awardsGet') ?>');
        $('#awards').modal('show');
        $('#game-finalized').hide();
    }

    function playersGet() {
        $("#players").load('<?= site_url('boards/playersGet') ?>');
        $('#players').modal('show');
    }

    function gamesGet() {
        $("#games").load('<?= site_url('games/gamesGet') ?>');
        $('#games').modal('show');
    }

    function paymentsGet() {
        $("#payments").load('<?= site_url('payments/paymentsGet') ?>');
        $('#payments').modal('show');
    }

    function settingsGet() {
        $("#settings").load('<?= site_url('home/settingsGet') ?>');
        $('#settings').modal('show');
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

    document.getElementById("toggle-messages-btn").addEventListener("click", function() {
        const messageContainer = document.getElementById("message-display-container");
        
        if (messageContainer.style.display === "none" || messageContainer.style.display === "") {
            messageContainer.style.display = "flex";
        } else {
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

    function generateNumber() {
        if (numbersgenerated.length >= 75) {
            clearInterval(generationInterval);
            console.log("all numbers have been generated");
            return; 
        }

        $.ajax({
            url: '<?= site_url('boards/numberSubmit') ?>',
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
                } else {
                    let newNumber = data.number;

                    if (numbersgenerated.includes(newNumber)) {
                        generateNumber();
                    } else {
                        numbersgenerated.push(newNumber);

                        $("#generated-number").html('<span>' + newNumber + '</span>').addClass("move-number");

                        lastNumbers.push(newNumber);

                        if (lastNumbers.length > 6) {
                            lastNumbers.shift(); 
                        }

                        let latestUncurrent = lastNumbers.slice(0, -1);

                        $("#last-numbers").html("");

                        latestUncurrent.forEach(function(number) {
                            let classNumber = '';
                            if (number <= 15) {
                                classNumber = 'b-col';
                            } else if (number <= 30) {
                                classNumber = 'i-col';
                            } else if (number <= 45) {
                                classNumber = 'n-col';
                            } else if (number <= 60) {
                                classNumber = 'g-col';
                            } else if (number <= 75) {
                                classNumber = 'o-col';
                            }

                            $("#last-numbers").append('<div class="ball-number ball-60 ' + classNumber + '"><span>' + number + '</span></div>');
                        });

                        if (newNumber <= 15) {
                            $("#generated-number").removeClass().addClass('ball-number ball-90 b-col');
                        } else if (newNumber <= 30) {
                            $("#generated-number").removeClass().addClass('ball-number ball-90 i-col');
                        } else if (newNumber <= 45) {
                            $("#generated-number").removeClass().addClass('ball-number ball-90 n-col');
                        } else if (newNumber <= 60) {
                            $("#generated-number").removeClass().addClass('ball-number ball-90 g-col');
                        } else if (newNumber <= 75) {
                            $("#generated-number").removeClass().addClass('ball-number ball-90 o-col');
                        }

                        if (narrationPlaying) {
                            if (narrationAudio) {
                                narrationAudio.pause();
                                narrationAudio.currentTime = 0;
                            }
                            narrationAudio = new Audio('<?= site_url('assets/sounds/') ?>' + newNumber + '.mp3');
                            narrationAudio.play();
                        }

                        setTimeout(function () {
                            $("#generated-number").removeClass("move-number");
                        }, 1000);

                        if (newNumber <= 15) {
                            $("#generated-number").addClass('b-col');
                            $("#number-" + newNumber).removeClass().addClass('b-col highlighted');
                        } else if (newNumber <= 30) {
                            $("#generated-number").addClass('i-col');
                            $("#number-" + newNumber).removeClass().addClass('i-col highlighted');
                        } else if (newNumber <= 45) {
                            $("#generated-number").addClass('n-col');
                            $("#number-" + newNumber).removeClass().addClass('n-col highlighted');
                        } else if (newNumber <= 60) {
                            $("#generated-number").addClass('g-col');
                            $("#number-" + newNumber).removeClass().addClass('g-col highlighted');
                        } else if (newNumber <= 75) {
                            $("#generated-number").addClass('o-col');
                            $("#number-" + newNumber).removeClass().addClass('o-col highlighted');
                        }

                        setTimeout(function () {
                            $("#number-" + newNumber).addClass("move-number");
                        }, 500);

                        numbersgenerated.forEach(function(number) {
                            if (number !== newNumber) {
                                $("#number-" + number).removeClass('highlighted').addClass('marked');
                            }
                        });
                    }

                    if (data.status === 'completed') {
                        stopAutomaticGeneration();
                        stopupdateUserCount();
                        setTimeout(() => {
                            showGamefinalized();
                        }, 3000); 

                        var divToRemove = document.getElementById('controls');

                        if (divToRemove) {
                            divToRemove.remove();
                        } else {
                            console.log('element with id "controls" does not exist');
                        }
                    }
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

    function startAutomaticGeneration() {
        clearInterval(generationInterval);
        generationInterval = setInterval(generateNumber, 10000);
    }

    function stopAutomaticGeneration() {
        clearInterval(generationInterval);
        console.log("automatic generation stopped");
    }

    $('#start-button').click(function() {
        
        $(this).hide();
        
        $('#generated-number, #stop-button, #next-number-button').show();

        sendMessage('<?= translate('game started!'); ?> 😎', 26);
        
        setTimeout(function() {
            generateNumber(); 
        }, 2000); 
        
        setTimeout(function() {
            startAutomaticGeneration(); 
        }, 2000); 
    });

    $('#next-number-button').click(function() {
        clearInterval(generationInterval);
        generateNumber();
        startAutomaticGeneration();
    });

    $('#stop-button').click(function() {
        stopAutomaticGeneration();

        $('#stop-button').hide();
        $('#next-number-button').hide();
        $('#play-button').show();
    });

    $('#play-button').click(function() {
        startAutomaticGeneration();

        $('#play-button').hide();
        $('#stop-button').show();
        $('#next-number-button').show();
    });

    let userCountInterval;

    function updateUserCount() {
        $.ajax({
            url: '<?= site_url('boards/playersGetCount') ?>',
            type: 'GET',
            success: function (data) {
                if (data.userCount && data.userCount > 0) {
                    $('.count_notifications').text(data.userCount).show();
                } else {
                    $('.count_notifications').hide();
                }
            },
            error: function (xhr, status, error) {
                console.error('error getting user count:', error);
            }
        });
    }

    function stopupdateUserCount() {
        if (userCountInterval) {
            clearInterval(userCountInterval); 
            console.log("user counting has stopped automatically");
        }
    }

    $(document).ready(function () {
        userCountInterval = setInterval(updateUserCount, 1000);
        updateUserCount(); 
    });

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
                    console.log("narrator successfully disabled");
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
</script>