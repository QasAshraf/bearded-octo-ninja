canvasWidth = 1100; //window.innerWidth;
canvasHeight = 650; //(window.innerHeight / 100) * 80;

players = [];
playerPositions = [];
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
  easterEggs();

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
    if (json.operation === "PLAYER" && "type" === "move") {
      movePlayer(0, parseInt(json.x), parseInt(json.y));
    } else if (json.operation === "PLAYER" && "type" === "new") {
      addPlayer();
    } else if (json.operation === "GAME" && json.type === "new") {
      grid = json.grid;
      gridWidth = grid[0].length;
      gridHeight = grid.length;

      mazeBlockWidth = Math.round(canvasWidth / gridWidth);
      mazeBlockHeight = Math.round(canvasHeight / gridHeight);

      buildMaze();

      // Add the layer to the stage
      stage.add(layer);

      // Add test players
      addPlayer();
      addPlayer();
      addPlayer();
      // addPlayer();
      // addPlayer();
      stackPlayers(startX,startY);
    } else if (json.operation === "GAME" && json.type === "end") {

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
    if (grid[j][i] === " ") {
       return;
    } else if (grid[j][i] === "#") {
      blockFill = "rgb(100,100,100)";
    } else if (grid[j][i] === "s") {
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
    var newPlayer = new Kinetic.Rect({
      x: startX * mazeBlockWidth,
      y: startY * mazeBlockHeight,
      width: mazeBlockWidth,
      height: mazeBlockHeight,
      fill: getRandomColor()
    });

    players.push(newPlayer);
    playerPositions.push([startX,startY]);

    // add the shape to the layer
    layer.add(newPlayer);

    layer.draw();
  }

  function stackPlayers(i,j) {
    var stackedPlayers = [];
    for (var k = 0; k < players.length; k++) {
      if (playerPositions[k][0] === i && playerPositions[k][1] === j) {
        stackedPlayers.push(players[k]);
      }
      console.log(playerPositions[k][0],playerPositions[k][1]);
    }

    console.log(stackedPlayers.length);

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

  function movePlayer(playerIndex, i, j) {
    players[playerIndex].setWidth(mazeBlockWidth);
    players[playerIndex].setHeight(mazeBlockHeight);

    var oldX = playerPositions[playerIndex][0];
    var oldY = playerPositions[playerIndex][1];

    playerPositions[playerIndex] = [i,j];
    stackPlayers(oldX,oldY);

    var tween = new Kinetic.Tween({
      node: players[playerIndex], 
      duration: 1,
      x: i * mazeBlockWidth,
      y: j * mazeBlockHeight,
      opacity: 1,
      onFinish: function() {
        stackPlayers(playerPositions[playerIndex][0],playerPositions[playerIndex][1]);
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

  function easterEggs() {
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