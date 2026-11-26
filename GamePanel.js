const games = [

    {
        name: "Patintero",
        image: "PT.png",

        description:
            "Patintero is a traditional Filipino outdoor game where two teams compete against each other. One team guards the marked lines while the opposing team attempts to cross without getting tagged.",

        players:
            "At least 6 players (3 or more players per team).",

        materials:
            "Chalk,",

        instructions: [
            "Divide the players into two teams.",
            "Draw a rectangular grid on the ground.",
            "One team guards the marked lines.",
            "The other team attempts to cross without getting tagged.",
            "Switch roles after each round."
        ]
    },

    {
        name: "Tumbang Preso",
        image: "TP.png"
    },

    {
        name: "Chinese Garter",
        image: "CG.png"
    },

    {
        name: "Langit Lupa",
        image: "LL.png"
    },

    {
        name: "Tago-Taguan",
        image: "TT.png"
    },

    {
        name: "Luksong Baka",
        image: "LB.png"
    },

    {
        name: "Luksong Tinik",
        image: "LT.png"
    },

    {
        name: "Agawan Base",
        image: "AB.png"
    },

    {
        name: "Piko",
        image: "PK.png"
    },

    {
        name: "Sungka",
        image: "SK.png"
    }

];


/* =====================================
   CURRENT SLIDE
===================================== */

let currentSlide = 0;


/* =====================================
   DISPLAY GAME INFORMATION
===================================== */

function showSlide(index) {

    currentSlide = index;

    const game = games[currentSlide];

    // Update game title
    document.getElementById("game-title").textContent =
        game.name;

    // Update game image
    document.getElementById("game-image").src =
        game.image;

    document.getElementById("game-image").alt =
        game.name;

    // Update game information
    document.getElementById("game-name").textContent =
        game.name;

    document.getElementById("game-description").textContent =
        game.description || "";

    document.getElementById("game-players").textContent =
        game.players || "";

    document.getElementById("game-materials").textContent =
        game.materials || "";

    // Display instructions as a numbered list
    document.getElementById("game-instructions").innerHTML =
        game.instructions
            ? game.instructions.map(step => `<li>${step}</li>`).join("")
            : "";

    // Update active indicator
    updateIndicators();

}


/* =====================================
   NEXT SLIDE
===================================== */

function nextSlide() {

    currentSlide++;

    if (currentSlide >= games.length) {
        currentSlide = 0;
    }

    showSlide(currentSlide);

}


/* =====================================
   PREVIOUS SLIDE
===================================== */

function previousSlide() {

    currentSlide--;

    if (currentSlide < 0) {
        currentSlide = games.length - 1;
    }

    showSlide(currentSlide);

}


/* =====================================
   CREATE SLIDE INDICATORS
===================================== */

function createIndicators() {

    const indicatorContainer =
        document.getElementById("indicators");

    indicatorContainer.innerHTML = "";

    games.forEach((game, index) => {

        const button = document.createElement("button");

        button.type = "button";

        button.setAttribute(
            "aria-label",
            "Go to " + game.name
        );

        button.addEventListener("click", function() {

            showSlide(index);

        });

        indicatorContainer.appendChild(button);

    });

    updateIndicators();

}


/* =====================================
   UPDATE ACTIVE INDICATOR
===================================== */

function updateIndicators() {

    const indicators =
        document.querySelectorAll(".indicators button");

    indicators.forEach((indicator, index) => {

        if (index === currentSlide) {

            indicator.classList.add("active");

        } else {

            indicator.classList.remove("active");

        }

    });

}


/* =====================================
   INITIALIZE CAROUSEL
===================================== */

// Generate indicators
createIndicators();

// Display the first game
showSlide(0);