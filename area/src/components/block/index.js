import React, { useState, useContext, useffect } from 'react'
import Draggable from 'react-draggable'
import { RectangleBlock, RectangleWrapper, AutomationText, LogoWrapper, AutomationRectangle, ArrowRectangle, CircleArcBot, CircleArcTop } from './blockElements'
import MyContext from '../Context'
import { Icon } from '@iconify/react';

// function block that allow the user to move, drag and remove some blocks
const Block = (props) => {
  const [backgroundColor, setbackgroundColor] = useState(props.color)
  const [pos, setPos] = useState({ x: props.top, y: props.left })
  const { sharedData, setSharedData } = useContext(MyContext);
  const { linkedList, setLinkedList } = useContext(MyContext);
  const { open, setOpen } = useContext(MyContext);
  const {ID, setID} = useContext(MyContext);

  //  function that allow the user to open the block in order to write some infos in the block
  const handleOpen = (e, data) => {
    if (open === null) {
      setOpen(props.id);
    } else if (open == !props.id) {
      setOpen(props.id);
    } else {
      setOpen(null);
    }
  };

  //  function that handle the drag of the block
  const handleDrag = (e) => {
    var rect = e.target.getBoundingClientRect();
    setOpen(null)

    for (var i = 0; i < sharedData.length; i++) {
      if (props.id !== sharedData[i].index) {
        if (rect.top > sharedData[i].top + 110 && rect.top < sharedData[i].top + 130 && rect.left > sharedData[i].left - 10 && rect.left < sharedData[i].left + 10) {
          if (props.action === false) {
            setbackgroundColor('black')
            break;
          }
        } else
          setbackgroundColor(props.color)
      }
    }
  }

  //  function that get the id of the block above the current block in order to link them
  const getIdAboveMe = (id) => {
    for (var i = 0; i < sharedData.length; i++) {
      if (id !== sharedData[i].index) {
        if (sharedData[id].top > sharedData[i].top + 110 && sharedData[id].top < sharedData[i].top + 130 && sharedData[id].left > sharedData[i].left - 10 && sharedData[id].left < sharedData[i].left + 10)
          return sharedData[i].index
      }
    }
    return;
  }

  //  function check different things when the block is released
  const handleDragStop = (e) => {
    var rect = e.target.getBoundingClientRect();
    const screenWidth = window.innerWidth;
    const screenHeight = window.innerHeight;

    //reset the block with the right information
    setbackgroundColor(props.color)
    sharedData[props.id].above = getIdAboveMe(props.id)
    for (var x = 0; x < linkedList.length; x++) {
      if (linkedList[x] === props.id && props.action == false) {
        linkedList.splice(x, linkedList.length - x)
      }
    }

    // Set the new position in SharedData
    for (var i = 0; i < sharedData.length; i++) {
      if (props.id === sharedData[i].index) {
        sharedData[i].top = rect.top;
        sharedData[i].left = rect.left;
      }
    }

    // Check if the block is above another block
    for (var i = 0; i < sharedData.length; i++) {
      if (props.id === sharedData[i].index)
        continue;
      // If the block is above another block with coordinates (top + 110, top + 130, left - 10, left + 10)
      if (rect.top > sharedData[i].top + 110 && rect.top < sharedData[i].top + 130 && rect.left > sharedData[i].left - 10 && rect.left < sharedData[i].left + 10) {
        // If the block is not an action block
        if (props.action === false) {
          setbackgroundColor('black')
          sharedData[props.id].above = getIdAboveMe(props.id)
          break;
        }
      }
    }

    // Set the new position in the linkedList
    for (var i = 0; i < linkedList.length; i++) {
      if (linkedList[i] === sharedData[props.id].above)
        linkedList.splice(i + 1, 0, props.id)
    }

    // check if the Block is in the bin 
    if (rect.left >= screenWidth * 0.85 && rect.top >= screenHeight * 0.25 && rect.top <= screenHeight * 0.61) {
      const indexToRemove = sharedData.findIndex((item) => item.index === props.id);
      if (indexToRemove !== -1) {
        sharedData.splice(indexToRemove, 1);
        sharedData.forEach((item, i) => {
          item.index = i;
        });
        setSharedData([...sharedData]);
        setID(sharedData.length);
      }
    }
  }

  //  function get icon to display the right icon in the block compare to the right service
  function getIcon(string) {
    switch (string) {
      case "discord":
        return "ic:baseline-discord";
      case "spotify":
        return "mdi:spotify";
      case "twitch":
        return "mdi:twitch";
      case "gmail":
        return "logos:google-gmail";
      case "twitter":
        return "mdi:twitter";
      case "github":
        return "mdi:github";
      default:
        return "mdi:github";
    }
  }

  return (
    <Draggable bounds='parent' onDrag={handleDrag} onStop={handleDragStop} >
      <RectangleBlock color={backgroundColor} top={pos.x} left={pos.y}>
        <RectangleWrapper>
          {(props.action === false) ? <CircleArcTop background="#ebebeb" /> : null}
          <CircleArcBot background={backgroundColor} />
          <AutomationText>
            {props.name}
          </AutomationText>
          <LogoWrapper>
            <AutomationRectangle>
              {/* put the icon in the block */}
              <Icon icon={getIcon(props.service)} width="35" height="35" color="white" />
            </AutomationRectangle>
            <ArrowRectangle onClick={() => { handleOpen() }}>
              {(open === props.id) ?
              //  put an arrow in the block and handle it if the arrow is open or not
                <Icon icon="material-symbols:arrow-back-ios-rounded" color="white" width="55" height="55" /> :
                <Icon icon="material-symbols:arrow-forward-ios-rounded" color="white" width="55" height="55" />
              }
            </ArrowRectangle>
          </LogoWrapper>
        </RectangleWrapper>
      </RectangleBlock>
    </Draggable>
  )
}

export default Block