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
          data: {
            ...a.data,
            [name]: text
          }
        }))
      else {
        let puzzleBlocksListTmp = puzzleBlocksList
        puzzleBlocksListTmp[stateSideBar[1].key]["data"] = {...puzzleBlocksListTmp[stateSideBar[1].key].data, [name]: text}
        setPuzzleBlocksList(puzzleBlocksListTmp)
      }
    }, [stateSideBar, puzzleBlocksList])

  const saveDataDropdownlistCurrentAction = useCallback(function(name, value) {
    if (stateSideBar[1].isActionPuzzleBlock)
      setDataActionPuzzleBlock(a => ({
        ...a,
        data: {
          ...a.data,
          [name]: value
        }
      }))
    else {
      let puzzleBlocksListTmp = puzzleBlocksList
      puzzleBlocksListTmp[stateSideBar[1].key]["data"] = {...puzzleBlocksListTmp[stateSideBar[1].key].data, [name]: value}
      setPuzzleBlocksList(puzzleBlocksListTmp)
    }
  }, [stateSideBar, puzzleBlocksList])

  const deletePuzzleBlock = useCallback(function() {
    if (stateSideBar[1].isActionPuzzleBlock) {
      setActionPuzzleBlock(null)
      setDataActionPuzzleBlock(null)
    } else {
      let puzzleBlocksListTmp = [];
      for(let i = 0; i < puzzleBlocksList.length; i++) {
        if (i === stateSideBar[1].key)
          continue
        puzzleBlocksListTmp.push(puzzleBlocksList[i])
      }
      for(let i = 0; i < puzzleBlocksListTmp.length; i++)
        puzzleBlocksListTmp[i]["key"] = i
      setPuzzleBlocksList(puzzleBlocksListTmp)
    }
    handleSlideSideBar(null)
  }, [stateSideBar, puzzleBlocksList])

  return (
    <View style={[styles.sideBar, styles.elevation, stateSideBar[0] ? {width: '75%', backgroundColor: stateSideBar[1].bgColor} : null]}>
      <View style={{flex: 0.5, justifyContent: 'center', alignItems: 'center', paddingLeft: '10%'}}>
        <TouchableOpacity activeOpacity={0.6} onPressOut={() => handleSlideSideBar(null)} style={{position: 'absolute', left: 0, top: '47%'}}>
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
                                defaultValue={stateSideBar[1].isActionPuzzleBlock ? dataActionPuzzleBlock["data"] ? dataActionPuzzleBlock["data"][nameField] : null
                                  : puzzleBlocksList[stateSideBar[1].key]["data"] ? puzzleBlocksList[stateSideBar[1].key]["data"][nameField] : null}
                                style={styles.sideBarInputText}/>
                break;
              case "dropdown":
                typeElement = <DropDownList name={element.name} uri={element.uri}
                                defaultValue={stateSideBar[1].isActionPuzzleBlock ? dataActionPuzzleBlock["data"] ? dataActionPuzzleBlock["data"][nameField] : null
                                  : puzzleBlocksList[stateSideBar[1].key]["data"] ? puzzleBlocksList[stateSideBar[1].key]["data"][nameField] : null}
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
          : <Text style={{color: white, fontSize: 16, fontStyle: 'italic'}}>No need to filling some fields</Text> : null
        }
        <TouchableOpacity activeOpacity={0.6} onPressOut={deletePuzzleBlock} style={stateSideBar[0] ? {position: 'absolute', bottom: 0, right: 25} : null}>
          <Ionicons name="md-trash-sharp" size={60} color={white}/>
        </TouchableOpacity>
      </View>
    </View>
  )
}