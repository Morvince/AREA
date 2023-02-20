import React, { useState } from 'react'
import Draggable from 'react-draggable'
import { RectangleBlock } from './blockElements'
import MyContext from '../Context'

const Block = (props) => {
  const [backgroundColor, setbackgroundColor] = useState(props.color)
  const [pos, setPos] = useState({ x: props.top, y: props.left })
  const { sharedData, setSharedData } = React.useContext(MyContext);
  const { linkedList, setLinkedList } = React.useContext(MyContext);
  const { playlist, setPlaylist } = React.useContext(MyContext);

  const [open, setOpen] = React.useState(false);

  const handleOpen = () => {
    setOpen(!open);
  };

  const [name, setName] = useState('');
  const [desc, setDesc] = useState('');


  React.useEffect(() => {
  }, [sharedData]);

  const handleChangeDesc = (event) => {
    setName(event.target.value);
    sharedData[props.id].toSend = { name: event.target.value, desc: desc, playlist_id: playlist.playlists[0].id }
  };

  const handleChangeName = (event) => {
    setDesc(event.target.value);
    sharedData[props.id].toSend = { name: event.target.value, desc: desc, playlist_id: playlist.playlists[0].id }
  };

  const handleDrag = (e, data) => {
    var rect = e.target.getBoundingClientRect();

    for (var i = 0; i < sharedData.length; i++) {
      if (props.id !== sharedData[i].index) {
        if (rect.top > sharedData[i].top + 110 && rect.top < sharedData[i].top + 130 && rect.left > sharedData[i].left - 10 && rect.left < sharedData[i].left + 10) {
          if (props.action === false) {
            setbackgroundColor('red')
            break;
          }
        } else {
          setbackgroundColor(props.color)
        }
      }
    }
  }

  const getIdAboveMe = (id) => {
    for (var i = 0; i < sharedData.length; i++) {
      if (id !== sharedData[i].index) {
        if (sharedData[id].top > sharedData[i].top + 110 && sharedData[id].top < sharedData[i].top + 130 && sharedData[id].left > sharedData[i].left - 10 && sharedData[id].left < sharedData[i].left + 10) {
          return sharedData[i].index
        }
      }
    }
    return;
  }

  const handleDragStop = (e, data) => {
    var rect = e.target.getBoundingClientRect();

    for (var i = 0; i < sharedData.length; i++) {
      if (props.id === sharedData[i].index) {
        sharedData[i].top = rect.top;
        sharedData[i].left = rect.left;
      }
    }

    for (var i = 0; i < sharedData.length; i++) {
      if (props.id !== sharedData[i].index) {
        if (rect.top > sharedData[i].top + 110 && rect.top < sharedData[i].top + 130 && rect.left > sharedData[i].left - 10 && rect.left < sharedData[i].left + 10) {
          if (props.action === false) {
            setbackgroundColor('red')
            sharedData[props.id].above = getIdAboveMe(props.id)
            break;
          }
        } else {
          setbackgroundColor(props.color)
          sharedData[props.id].above = getIdAboveMe(props.id)
          for (var x = 0; x < linkedList.length; x++) {
            if (linkedList[x] === props.id && props.action == false) {
              linkedList.splice(x, linkedList.length - x)
            }
          }
        }
      }
    }

    for (var i = 0; i < linkedList.length; i++) {
      if (linkedList[i] === sharedData[props.id].above) {
        linkedList.splice(i + 1, 0, props.id)
      }
    }

    // console.log("BEFORE IF");
    // console.log(sharedData);
    const screenWidth = window.innerWidth;
    const screenHeight = window.innerHeight;
    if (rect.left >= screenWidth * 0.85 && rect.top >= screenHeight * 0.25 && rect.top <= screenHeight * 0.61) {
      // console.log("BEFORE CUT")
      // console.log(sharedData);
      const indexToRemove = sharedData.findIndex((item) => item.index === props.id);
      // console.log("index to remove : ");
      // console.log(indexToRemove);
      if (indexToRemove !== -1) {
        sharedData.splice(indexToRemove, 1);
        sharedData.forEach((item, i) => {
        item.index = i;
        });
        setSharedData([...sharedData]);
        // console.log("AFTER CUT")
        // console.log("sharedData");
        // console.log(sharedData);
      }
    }
  }

  function renderInput() {
    if (props.nbrBox === 2) {
      return (
        <div>
          <input id="name" type="text" placeholder="Name" onChange={handleChangeName} />
          <input id="description" type="text" placeholder="Description" onChange={handleChangeDesc} />
        </div>
      )
    }
  }

  return (
    <Draggable bounds='parent' onDrag={handleDrag} onStop={handleDragStop}>
      <RectangleBlock color={backgroundColor} top={pos.x} left={pos.y}>
        {props.name}
        {renderInput()}
      </RectangleBlock>
    </Draggable>
  )
}

export default Block