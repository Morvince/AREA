import React, { useState, useRef } from 'react';

export const Rectangle = (props) => {
  const [position, setPosition] = useState({ x: props.x, y: props.y });
  const ref = useRef(null);

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

    const handleMouseUp = () => {
      window.removeEventListener('mousemove', handleMouseMove);
      window.removeEventListener('mouseup', handleMouseUp);
    }

    window.addEventListener('mouseup', handleMouseUp);
  }

  return (
    <div
      ref={ref}
      onMouseDown={handleMouseDown}
      style={{
        width: '250px',
        height: '150px',
        position: 'absolute',
        left: position.x,
        top: position.y,
        background: props.color,
        borderRadius: '20%',
        border: '3px solid black',
      }}
    />
  );
}