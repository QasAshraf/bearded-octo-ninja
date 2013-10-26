grid = [
        [" ", " ", " ", " ", " ", " "],
        [" ", "#", "#", "#", "#", " "],
        [" ", "#", "#", "#", " ", " "],
        [" ", "#", "#", "#", " ", " "],
        [" ", "#", "#", " ", " ", " "],
        [" ", "#", " ", " ", " ", " "]
       ];


$(document).ready(function() {

  var conn = new WebSocket('ws://109.109.137.94:8080');
  conn.onopen = function(e) {
    console.log("Connection established!");
  };

  conn.onmessage = function(e) {
    console.log(e.data);
    splitData = e.data.split(" ");
    if (splitData[0] === "move") {
      movePlayer(splitData[1], splitData[2], splitData[3]);
    } else if (splitData[0] === "add") {
      addPlayer();
    }
  };

  conn.onclose = function(e) {
    console.log("Connection closed!");
  };

  stage = new Kinetic.Stage({
    container: "game-container",
    width: window.innerWidth,
    height: (window.innerHeight / 100) * 80
  });
  layer = new Kinetic.Layer();
  players = [];
  velocity = [5,5];

  // Add the layer to the stage
  stage.add(layer);

  // Add test players
  addPlayer();

  // Test move
  setTimeout(function() {
    movePlayer(players[0], 800, 500);
  }, 1000);

  console.log(parseInt($("canvas").height()));

});

function addPlayer() {
  var newPlayer = new Kinetic.Rect({
    x: 10,
    y: 10,
    width: 50,
    height: 50,
    fill: 'green',
    stroke: 'black',
    strokeWidth: 0
  });

  players.push(newPlayer);

  // add the shape to the layer
  layer.add(newPlayer);
}

function movePlayer(player, x, y) {

  var anim = new Kinetic.Animation(function(frame) {
      var distanceToX = player.getPosition().x - x;
      var distanceToY = player.getPosition().y - y;
      // console.log(distanceToX,distanceToY);
      if (distanceToX > 0) {
        player.move(velocity[0] * -1,0);
      } else if (distanceToX < 0) {
        player.move(velocity[0],0);
      }

      if (distanceToY > 0) {
        player.move(0, velocity[1] * -1);
      } else if (distanceToY < 0) {
        player.move(0, velocity[1]);
      }

      if (distanceToX === 0 && distanceToY === 0) {
        this.stop();
      }
  }, layer);
  anim.start();

  player.move(velocity[0], velocity[1]);

}