canvasWidth = (window.innerWidth / 100) * 70;
canvasHeight = (window.innerHeight / 100) * 80;

players = [];
playerPositions = [];
playerNames = [];
playerNumbers = [];
playerColours = [];
velocity = [2,2];

startX = 0;
startY = 0;

$(document).ready(function() {

  stage = new Kinetic.Stage({
    container: "game-container",
    width: canvasWidth,
    height: canvasHeight
  });
  layer = new Kinetic.Layer();

  // Super shiny header
  herps();

  conn = new WebSocket('ws://109.109.137.94:8080');
  
  conn.onopen = function(e) {
    console.log("Connection established!");
    var gridRequest = '{"operation":"GAME","type":"create","message":"crystalmaze","size":"21","sender":"steph","id":388542958}';
    conn.send(gridRequest);
  };

  conn.onclose = function(e) {
    console.log("Connection closed!");
  };

  conn.onmessage = function(e) {
    var json = JSON.parse(e.data);
    console.log(json);

    var playerIndex = -1;
    for (var k = 0; k < playerNumbers.length; k++) {
      if (playerNumbers[k] === json.player) {
        playerIndex = k;
      }
    }
    if (json.operation === "PLAYER" && json.type === "move") {
      
      if (playerIndex > -1) {
        movePlayer(playerIndex, parseInt(json.x), parseInt(json.y));
      } else {
        console.log("Couldn't find player " + json.player);
      }
      $("#console").append("<p>" + colouredName(playerIndex) + " moved to (" + json.x + "," + json.y + ")</p>");
      // $("#console").scrollTo('100%');
    } else if (json.operation === "PLAYER" && json.type === "join") {
      playerNames.push(json.name);
      playerNumbers.push(json.player);
      addPlayer();
      $("#console").append("<p>" + colouredName(playerNumbers.length - 1) + " joined</p>");
      // $("#console").scrollTo('100%');
    } else if (json.operation === "GAME" && json.type === "new") {
      grid = json.grid;
      gridWidth = grid[0].length;
      gridHeight = grid.length;

      mazeBlockWidth = Math.round(canvasWidth / gridWidth);
      mazeBlockHeight = Math.round(canvasHeight / gridHeight);

      buildMaze();

      // Add the layer to the stage
      stage.add(layer);

    } else if (json.operation === "GAME" && json.type === "win") {
      $("#win-box").append("<h2>" + playerNames[playerIndex] + " are win!!!</h2>");
      setInterval(function() {
        $("#win-box h2").css({ color: getRandomColor() });
      }, 100);
      $("#win-box").css({ 
        position: "fixed",
        display: "block"
      });
    }
  };

});

  function buildMaze() {
    for (var i = 0; i < grid.length; i++) {
      for (var j = 0; j < grid[i].length; j++) {
        buildMazeBlock(i,j);
      };
    };
  }

  function buildMazeBlock(i,j) {
    var blockFill = ""
    if (grid[i][j] === " ") {
       return;
    } else if (grid[i][j] === "#") {
      blockFill = "rgb(100,100,100)";
    } else if (grid[i][j] === "s") {
      startX = i;
      startY = j;
      return;
    }

    var block = new Kinetic.Rect({
      x: i * mazeBlockWidth,
      y: j * mazeBlockHeight,
      width: mazeBlockWidth,
      height: mazeBlockHeight,
      fill: blockFill
    });

    if (grid[j][i] === "e") {
      setInterval(function() {
        block.setFill(getRandomColor());
        layer.draw();
      },100);
    }

    layer.add(block);
    layer.draw();
  }

  function addPlayer() {
    var colour = getRandomColor();
    playerColours.push(colour);

    var newPlayer = new Kinetic.Rect({
      x: startX * mazeBlockWidth,
      y: startY * mazeBlockHeight,
      width: mazeBlockWidth,
      height: mazeBlockHeight,
      fill: colour
    });

    players.push(newPlayer);
    playerPositions.push([startX,startY]);

    // add the shape to the layer
    layer.add(newPlayer);
    stackPlayers(startX,startY);
    layer.draw();
  }

  function stackPlayers(i,j) {
    var stackedPlayers = [];
    for (var k = 0; k < players.length; k++) {
      if (playerPositions[k][0] === i && playerPositions[k][1] === j) {
        stackedPlayers.push(players[k]);
      }
    }

    var stackRows = Math.ceil(stackedPlayers.length / 2);
    for (var k = 0; k < stackedPlayers.length; k++) {
      var row = Math.floor(k / 2);
      var column = k % 2;

      // Adjust x and y
      stackedPlayers[k].setPosition(
        i * mazeBlockWidth + column * Math.round(mazeBlockWidth / 2),
        j * mazeBlockHeight + row * Math.round(mazeBlockHeight / stackRows)
      );

      // Adjust width and height
      if (stackedPlayers.length % 2 === 0 || row != (stackRows - 1)) {
        stackedPlayers[k].setWidth(Math.round(mazeBlockWidth / 2));
      } else {
        stackedPlayers[k].setWidth(mazeBlockWidth);
      }

      stackedPlayers[k].setHeight(Math.ceil(mazeBlockHeight / stackRows));
    }

    layer.draw();

  }

  function movePlayer(pIndex, i, j) {
    players[pIndex].setWidth(mazeBlockWidth);
    players[pIndex].setHeight(mazeBlockHeight);

    var oldX = playerPositions[pIndex][0];
    var oldY = playerPositions[pIndex][1];

    playerPositions[pIndex] = [i,j];
    stackPlayers(oldX,oldY);

    var tween = new Kinetic.Tween({
      node: players[pIndex], 
      duration: 1,
      x: i * mazeBlockWidth,
      y: j * mazeBlockHeight,
      opacity: 1,
      onFinish: function() {
        stackPlayers(playerPositions[pIndex][0],playerPositions[pIndex][1]);
      }
    });

    tween.play();
  }

  function whatIsWallWhatisNot() {
    $(".kineticjs-content").css({ background: "rgb(100,100,100)" });
    setTimeout(function() {
      $(".kineticjs-content").css({ background: "rgb(220,220,220)" });
    },2000);
  }

  function getRandomColor() {
      var letters = '0123456789ABCDEF'.split('');
      var color = '#';
      for (var i = 0; i < 6; i++ ) {
          color += letters[Math.round(Math.random() * 15)];
      }
      return color;
  }

  function herps() {

    var interval; 
    $("header").mouseover(function() {
      interval = setInterval(function() {
        $("h1").css({ color: getRandomColor() });
      }, 100);
    });
    $("header").mouseout(function() {
      clearInterval(interval);
      $("h1").css({ color: "rgb(80,80,80)" });
    });
  }

  function colouredName(plIndex) {
    return "<span style='color: " + playerColours[plIndex] + "'>" + playerNames[plIndex] + "</span>";
  }