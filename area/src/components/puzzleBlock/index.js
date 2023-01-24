import React, { useState, useRef } from 'react';
import { Rectangle, Barre} from './puzzleBlockElements';
import 'animate.css/animate.min.css';

const Mouvement = (props) => {
  const [position, setPosition] = useState({ x: props.x, y: props.y });
  const initialPosition = useRef({ x: props.x, y: props.y });

  const handleMouseDown = (e) => {
    const initialX = e.clientX - position.x;
    const initialY = e.clientY - position.y;

    const handleMouseMove = (e) => {
      setPosition({
        x: e.clientX - initialX,
        y: e.clientY - initialY
      });
    }

    window.addEventListener('mousemove', handleMouseMove);

    const handleMouseUp = (e) => {
      window.removeEventListener('mousemove', handleMouseMove);
      window.removeEventListener('mouseup', handleMouseUp);
      props.onMouseUp(initialPosition.current, e.clientX, props.id);
    }

    window.addEventListener('mouseup', handleMouseUp);
  }

  return (
    <Rectangle
      x={position.x + "px"}
      y={position.y + "px"}
      color={props.color}
      onMouseDown={handleMouseDown}
      onMouseEnter={props.onMouseEnter}
      onMouseLeave={props.onMouseLeave}
      style={{backgroundColor: props.highlightedId === props.id ? 'green' : props.color}}
    />
  );
}

const PuzzeBlock = () => {
  const [rectangles, setRectangles] = useState([{id:1, x: 300, y: 150, color: '#686f84'}, 
                                                {id:2, x: 300, y: 350, color: '#686f84'}, 
                                                {id:3, x: 300, y: 550, color: '#686f84'}, 
                                                {id:4, x: 300, y: 750, color: '#686f84'}]);
  const [highlightedId, setHighlightedId] = useState(null);

  const handleMouseUp = (initialPosition, x, id) => {
    if(x < 600){
      setRectangles(rectangles.filter(rectangle => rectangle.id !== id));
    } else {
      setRectangles([...rectangles, {id: rectangles.length + 1, x: initialPosition.x, y: initialPosition.y, color: '#686f84'}]);
    }
  }

  const handleMouseEnter = (id) => {
    setHighlightedId(id);
  }

  const handleMouseLeave = () => {
    setHighlightedId(null);
  }

  return (
    <div>
      <Barre left="600px" />
      <Barre left="260px" />
      {rectangles.map((rect, index) => (<Mouvement 
      key={rect.id} 
      id={rect.id} 
      x={rect.x}
      y={rect.y}
      color={rect.color}
      onMouseUp={handleMouseUp}
      onMouseEnter={() => handleMouseEnter(rect.id)}
      onMouseLeave={handleMouseLeave}
      highlightedId={highlightedId}
      /> ))}
    </div>
  )
}
export default PuzzeBlock
