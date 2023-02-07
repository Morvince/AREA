import React, { useState } from 'react'
import Draggable from 'react-draggable'
import { RectangleBlock } from './blockElements'
import MyContext from '../Context'
import { useGetUserPlaylist } from '../../api/apiSpotify';

const Block = (props) => {
  const [backgroundColor, setbackgroundColor] = useState(props.color)
  const [pos, setPos] = useState({ x: props.top, y: props.left })
  const { sharedData, setSharedData } = React.useContext(MyContext);
  const { linkedList, setLinkedList } = React.useContext(MyContext);
  const userPlaylist = useGetUserPlaylist();

  const [open, setOpen] = React.useState(false);

  const handleOpen = () => {
    setOpen(!open);
  };

  const [name, setName] = useState('');
  const [desc, setDesc] = useState('');

  React.useEffect(() => {
    userPlaylist.mutate()
  }, [sharedData]);

  const handleChangeDesc = (event) => {
    setName(event.target.value);
  };

  const handleChangeName = (event) => {
    setDesc(event.target.value);
  };

  const handleDrag = (e, data) => {
    var rect = e.target.getBoundingClientRect();

    for (var i = 0; i < sharedData.length; i++) {
      if (props.id !== sharedData[i].index) {
        if (rect.top > sharedData[i].top + 110 && rect.top < sharedData[i].top + 130 && rect.left > sharedData[i].left - 10 && rect.left < sharedData[i].left + 10) {
          setbackgroundColor('red')
          break;
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
          setbackgroundColor('red')
          sharedData[props.id].above = getIdAboveMe(props.id)
          break;
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

  if (userPlaylist.isSuccess) {
    console.log(userPlaylist.data.data)
  }

  return (
    <Draggable bounds='parent' onDrag={handleDrag} onStop={handleDragStop}>
      <RectangleBlock color={backgroundColor} top={pos.x} left={pos.y}>
        {props.name}
        {renderInput()}
        <div className="dropdown">
          <button onClick={handleOpen}>Dropdown</button>
          {open ? (
            <ul className="menu">
              <li className="menu-item">
                <button>Menu 1</button>
              </li>
              <li className="menu-item">
                <button>Menu 2</button>
              </li>
            </ul>
          ) : null}
        </div>
      </RectangleBlock>
    </Draggable>
  )
}

export default Block