grid = [
        [" ", " ", " ", " ", " ", " "],
        [" ", "#", "#", "#", "#", " "],
        [" ", "#", "#", "#", " ", " "],
        [" ", "#", "#", "#", " ", " "],
        [" ", "#", "#", " ", " ", " "],
        [" ", "#", " ", " ", " ", " "]
       ];


$(document).ready(function() {

  // var conn = new WebSocket('ws://localhost:8080');
  // conn.onopen = function(e) {
  //     console.log("Connection established!");
  // };

  // conn.onmessage = function(e) {
  //     console.log(e.data);
  // };

  stage = new Kinetic.Stage({
    container: "game-container",
    width: window.innerWidth,
    height: (window.innerWidth / 100) * 40
  });

  layer = new Kinetic.Layer();

  players = [];

  // add the layer to the stage
  stage.add(layer);

  addPlayer();
  setTimeout(addPlayer, 1000);

  var vector = [5,5];

  console.log(parseInt($("canvas").height()));
  
  var anim = new Kinetic.Animation(function(frame) {
    for (var i = 0; i < players.length; i++) {
      movePlayer(players[i], vector);
    }
  }, layer);
  anim.start();

});

function addPlayer() {
  var newPlayer = new Kinetic.Rect({
    x: 239,
    y: 75,
    width: 100,
    height: 50,
    fill: 'green',
    stroke: 'black',
    strokeWidth: 4
  });

  players.push(newPlayer);

  // add the shape to the layer
  layer.add(newPlayer);
}

function movePlayer(player, velocity) {

  // Check top collision
  if (player.getPosition().y <= 0) {
    console.log("top");
    velocity[1] *= -1;
  }

  // Check bottom collision
  if (player.getPosition().y + player.getHeight() >= parseInt($("canvas").height())) {
    console.log("bottom");
    velocity[1] *= -1;
  }

  // Check left collision
  if (player.getPosition().x <= 0) {
    console.log("left");
    velocity[0] *= -1;
  }

  // Check right collision
  if (player.getPosition().x + player.getWidth() >= parseInt($("canvas").width())) {
    console.log("right");
    velocity[0] *= -1;
  }

  player.move(velocity[0], velocity[1]);

}