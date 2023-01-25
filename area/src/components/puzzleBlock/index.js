import React, { useState, useRef } from 'react';
import { Rectangle, Bin, Bin2 } from './puzzleBlockElements';

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
    <>
      <Bin color={props.color}/>
      <Rectangle
      x={position.x + "px"}
      y={position.y + "px"}
      color={props.color}
      onMouseDown={handleMouseDown}
      onMouseEnter={props.onMouseEnter}
      onMouseLeave={props.onMouseLeave}
      style={{backgroundColor: props.highlightedId === props.id ? 'green' : props.color}}
      />
      < Bin2 />
    </>
  );
}

const PuzzeBlock = (props) => {
  const [rectangles, setRectangles] = useState([{id:1, x: props.x, y: props.y, color: props.color}]);
  const [highlightedId, setHighlightedId] = useState(null);

  const handleMouseUp = (initialPosition, newId) => {
    setRectangles([...rectangles, {id: newId, x: initialPosition.x, y: initialPosition.y}]);
  }

  const handleMouseEnter = (id) => {
    setHighlightedId(id);
  }

  const handleMouseLeave = () => {
    setHighlightedId(null);
  }

  return (
    <div>
      {rectangles.map(() => (
        <Mouvement
          id={props.id}
          x={props.x}
          y={props.y}
          color={props.color}
          onMouseUp={handleMouseUp}
          onMouseEnter={() => handleMouseEnter(props.id)}
          onMouseLeave={handleMouseLeave}
          highlightedId={highlightedId}
        />
      ))}
    </div>
  )
}
export default PuzzeBlock