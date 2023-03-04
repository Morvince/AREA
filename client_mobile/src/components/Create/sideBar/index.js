import { View, Text, TouchableOpacity, TextInput } from 'react-native';
import styles from '../styles';
import { MaterialIcons, Ionicons } from '@expo/vector-icons';
import { white } from '../../../color';
import DropDownList from './dropdownlist';
import { useCallback } from 'react';

export default function SideBar({stateSideBar, handleSlideSideBar, setActionPuzzleBlock, dataActionPuzzleBlock, setDataActionPuzzleBlock, puzzleBlocksList, setPuzzleBlocksList}) {
  let key = 0
  console.log(stateSideBar[1])

  const saveDataTextInputCurrentAction = useCallback(
    name => text => {
      if (stateSideBar[1].isActionPuzzleBlock)
        setDataActionPuzzleBlock(a => ({
          ...a,
          [name]: text
        }))
      else {
        let puzzleBlocksListTmp = puzzleBlocksList
        puzzleBlocksListTmp[stateSideBar[1].key][name] = text
        setPuzzleBlocksList(puzzleBlocksListTmp)
      }
    }, [stateSideBar, puzzleBlocksList])

  const saveDataDropdownlistCurrentAction = useCallback(function(name, value) {
    if (stateSideBar[1].isActionPuzzleBlock)
      setDataActionPuzzleBlock(a => ({
        ...a,
        [name]: value
      }))
    else {
      let puzzleBlocksListTmp = puzzleBlocksList
      puzzleBlocksListTmp[stateSideBar[1].key][name] = value
      setPuzzleBlocksList(puzzleBlocksListTmp)
    }
  }, [stateSideBar, puzzleBlocksList])

  const deletePuzzleBlock = useCallback(function() {
    if (stateSideBar[1].isActionPuzzleBlock) {
      setActionPuzzleBlock(null)
      setDataActionPuzzleBlock(null)
    }
    handleSlideSideBar(null)
  }, [stateSideBar])

  return (
    <View style={[styles.sideBar, styles.elevation, stateSideBar[0] ? {width: '75%', backgroundColor: stateSideBar[1].bgColor} : null]}>
      <View style={{flex: 0.5, justifyContent: 'center', alignItems: 'center', paddingLeft: '10%'}}>
        <TouchableOpacity activeOpacity={0.6} onPressOut={() => handleSlideSideBar(null)} style={{position: 'absolute', left: 0}}>
          <MaterialIcons name="keyboard-arrow-left" size={60} color={white}/>
        </TouchableOpacity>
        {stateSideBar[1] ? stateSideBar[1].fields !== null && stateSideBar[1].fields.length !== 0 ?
          stateSideBar[1].fields.map(element => {
            let typeElement = null
            let nameField = element.name
            key++
            switch (element.type) {
              case "text":
                typeElement = <TextInput placeholder={element.name} placeholderTextColor={white} multiline={true}
                                onChangeText={saveDataTextInputCurrentAction(element.name)}
                                defaultValue={stateSideBar[1].isActionPuzzleBlock ? dataActionPuzzleBlock[nameField] ? dataActionPuzzleBlock[nameField] : null
                                  : puzzleBlocksList[stateSideBar[1].key][nameField] ? puzzleBlocksList[stateSideBar[1].key][nameField] : null}
                                style={styles.sideBarInputText}/>
                break;
              case "dropdown":
                typeElement = <DropDownList name={element.name} uri={element.uri}
                                defaultValue={stateSideBar[1].isActionPuzzleBlock ? dataActionPuzzleBlock[nameField] ? dataActionPuzzleBlock[nameField] : null
                                  : puzzleBlocksList[stateSideBar[1].key][nameField] ? puzzleBlocksList[stateSideBar[1].key][nameField] : null}
                                saveDataDropdownlistCurrentAction={saveDataDropdownlistCurrentAction}/>
                break;
              default:
                break;
            }
            return (
              <View key={key}>
                <Text>{element.title}</Text>
                {typeElement}
              </View>
            )
          })
          : <Text>No need to filling fields</Text> : null
        }
        <TouchableOpacity activeOpacity={0.6} onPressOut={deletePuzzleBlock} style={stateSideBar[0] ? {position: 'absolute', bottom: 0, right: 25} : null}>
          <Ionicons name="md-trash-sharp" size={60} color={white}/>
        </TouchableOpacity>
      </View>
    </View>
  )
}