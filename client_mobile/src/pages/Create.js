import { View, Text, TouchableOpacity, TextInput, Button } from 'react-native';
import { useCallback, useEffect, useState } from 'react';
import styles from '../components/Create/styles';
import ServicesBar from '../components/Create/servicesBar/index';
import ActionsBar from '../components/Create/actionsBar/index';
import ActionsList from '../components/Create/actionsList/index';
import SideBar from '../components/Create/sideBar/index';
import { useGetAllActions, useAddAutomation } from '../api/apiCreatePage';
import { MaterialIcons, Feather } from '@expo/vector-icons';
import { black, darkGray, white } from '../color';
import AsyncStorage from '@react-native-async-storage/async-storage';

export default function Create({ navigation }) {
  const [slideServicesBar, setSlideServicesBar] = useState(true)
  const [slideActionsBar, setSlideActionsBar] = useState(null)
  const [puzzleBlocksList, setPuzzleBlocksList] = useState([])
  const [actionPuzzleBlock, setActionPuzzleBlock] = useState(null)
  const [dataActionPuzzleBlock, setDataActionPuzzleBlock] = useState(null)
  const [stateSideBar, setStateSideBar] = useState([false, null])
  const [displayEnterName, setDisplayEnterName] = useState(false)
  const [enterName, setEnterName] = useState(null)
  const handleStyleActionsBar = {discord: ["#7289da", "#5470d6"], spotify: ["#1db954", "#10a143"],
                                twitch: ["#6713e2", "#9146ff"], gmail: ["#d92516", "#EA4335"],
                                twitter: ["#1486cc", "#1da1f2"], github: [black, darkGray]}
  const getAllActions = useGetAllActions()
  const [allActions, setAllActions] = useState(null)
  const addAutomation = useAddAutomation()

  const handleSlideServicesBar = useCallback(function() {
    if (slideServicesBar)
      setSlideServicesBar(false)
    else
      setSlideServicesBar(true)
  }, [slideServicesBar])

  const handleSlideActionsBar = useCallback(function(service) {
    if (allActions === null || allActions === undefined)
      return
    if (slideActionsBar === service) {
      setSlideActionsBar(null)
      return
    }
    switch (service) {
      case "discord":
        setSlideActionsBar("discord")
        break;
      case "spotify":
        setSlideActionsBar("spotify")
        break;
      case "twitch":
        setSlideActionsBar("twitch")
        break;
      case "gmail":
        setSlideActionsBar("gmail")
        break;
      case "twitter":
        setSlideActionsBar("twitter")
        break;
      case "github":
        setSlideActionsBar("github")
        break;
      default:
        break;
    }
  }, [slideActionsBar, allActions])

  const handleSlideSideBar = useCallback (function(item) {
    setStateSideBar(s => [!s[0], item])
  }, [])

  const addPuzzleBlocksToList = useCallback(function(item) {
    if ((actionPuzzleBlock === null && item.type === "reaction") ||
      (actionPuzzleBlock !== null && item.type === "action"))
      return
    if (actionPuzzleBlock === null)
      setDataActionPuzzleBlock({bgColor: handleStyleActionsBar[slideActionsBar][1], isActionPuzzleBlock: true, ...item})
    actionPuzzleBlock === null ?
      setActionPuzzleBlock(
        <View style={[styles.actionBlocks, styles.elevation, {backgroundColor: handleStyleActionsBar[slideActionsBar][1]}]}>
          <Text style={styles.textActionBlocks}>{item.name}</Text>
          <TouchableOpacity activeOpacity={0.6} onPressOut={() => handleSlideSideBar({bgColor: handleStyleActionsBar[slideActionsBar][1], isActionPuzzleBlock: true, ...item})} style={styles.arrowActionBlocks}>
            <MaterialIcons name="keyboard-arrow-right" size={48} color={white}/>
          </TouchableOpacity>
        </View>
      )
      : setPuzzleBlocksList([...puzzleBlocksList, {key: puzzleBlocksList.length, bgColor: handleStyleActionsBar[slideActionsBar][1], isActionPuzzleBlock: false, ...item}])
  }, [actionPuzzleBlock, puzzleBlocksList, slideActionsBar])

  const submitArea = useCallback(function() {
    if (enterName === null || enterName === "") {
      setEnterName(null)
      alert("You must enter a name for your Area")
      return
    }
    if (dataActionPuzzleBlock.fields !== null && dataActionPuzzleBlock.fields.length !== 0) {
      for(let i = 0; i < dataActionPuzzleBlock.fields.length; i++) {
        if (dataActionPuzzleBlock.data) {
          if (dataActionPuzzleBlock.data.hasOwnProperty(dataActionPuzzleBlock.fields[i].name) === false) {
            setEnterName(null)
            setDisplayEnterName(false)
            alert("You must fill the fields for your Action")
            return
          }
        } else {
          setEnterName(null)
          setDisplayEnterName(false)
          alert("You must fill the fields for your Action")
          return
        }
      }
    }
    for (let i = 0; i < puzzleBlocksList.length; i++) {
      if (puzzleBlocksList[i].fields !== null && puzzleBlocksList[i].fields.length !== 0) {
        for(let j = 0; j < puzzleBlocksList[i].fields.length; j++) {
          if (puzzleBlocksList[i].data) {
            if (puzzleBlocksList[i].data.hasOwnProperty(puzzleBlocksList[i].fields[j].name) === false) {
              setEnterName(null)
              setDisplayEnterName(false)
              alert("You must fill the fields for your Reactions")
              return
            }
          } else {
            setEnterName(null)
            setDisplayEnterName(false)
            alert("You must fill the fields for your Reactions")
            return
          }
        }
      }
    }
    resetCreatePage()
  }, [actionPuzzleBlock, dataActionPuzzleBlock, puzzleBlocksList, enterName])

  const resetCreatePage = useCallback(function() {
    const actionsTmp = []
    // dataActionPuzzleBlock.data ? actionsTmp.push(dataActionPuzzleBlock.data) : null

    AsyncStorage.getItem("token")
    .then((res) => {
      addAutomation.mutate(JSON.stringify({
        token: res,
        name: enterName,
        actions: []
      }), {
        onSuccess: () => {
          alert("Success")
          setSlideServicesBar(true)
          setSlideActionsBar(null)
          setActionPuzzleBlock(null)
          setDataActionPuzzleBlock(null)
          setPuzzleBlocksList([])
          setStateSideBar([false, null])
          setDisplayEnterName(false)
          setEnterName(null)
        }
      })
    })
  }, [enterName])

  useEffect(() => {
    getAllActions.mutate(null, {
      onSuccess: (data) => {
        setAllActions(data.data.actions)
      }
    })
  }, [])

  console.log("puzzleBlocksList -> ", puzzleBlocksList)

  return (
    <View style={styles.container}>
      {displayEnterName ?
        <View style={styles.enterName}>
          <TextInput placeholder='Enter a name for your Area' onChangeText={(text) => setEnterName(text)}
            style={{paddingBottom: 10, fontSize: 16, textAlign: 'center'}}/>
          <Button title="OK" onPress={submitArea} color="#31e53f"/>
        </View>
        : null
      }
      <Feather name={slideServicesBar ? "arrow-down-circle" : "arrow-up-circle"} size={45} color={black} onPress={handleSlideServicesBar}
        style={[{position: 'absolute', bottom: 0, zIndex: 10}, slideServicesBar && {bottom: 80}, slideActionsBar === null ? null : {bottom: 205}, !slideServicesBar && {bottom: 0}]}
      />
      {actionPuzzleBlock === null ?
        <Text style={{position: 'absolute', fontSize: 17, fontStyle: 'italic', color: darkGray, top: '50%', opacity: 0.5}}>Place your pieces here</Text>
        : null
      }
      {actionPuzzleBlock}
      <ActionsList puzzleBlocksList={puzzleBlocksList} setPuzzleBlocksList={setPuzzleBlocksList}
        slideServicesBar={slideServicesBar} slideActionsBar={slideActionsBar}
        handleSlideSideBar={handleSlideSideBar}
      />
      <ActionsBar slideServicesBar={slideServicesBar} slideActionsBar={slideActionsBar}
        handleStyleActionsBar={handleStyleActionsBar} addPuzzleBlocksToList={addPuzzleBlocksToList}
        allActions={allActions} actionPuzzleBlock={actionPuzzleBlock}
      />
      <ServicesBar slideServicesBar={slideServicesBar} handleSlideActionsBar={handleSlideActionsBar}/>
      <SideBar stateSideBar={stateSideBar} handleSlideSideBar={handleSlideSideBar}
        setActionPuzzleBlock={setActionPuzzleBlock} dataActionPuzzleBlock={dataActionPuzzleBlock}
        setDataActionPuzzleBlock={setDataActionPuzzleBlock}
        puzzleBlocksList={puzzleBlocksList} setPuzzleBlocksList={setPuzzleBlocksList}/>
      {actionPuzzleBlock !== null && puzzleBlocksList.length !== 0 ?
        <TouchableOpacity activeOpacity={0.6} onPressOut={() => setDisplayEnterName(true)} style={{position: 'absolute', left: 5, top: '47%'}}>
          <MaterialIcons name="library-add-check" size={60} color="#31e53f"/>
        </TouchableOpacity>
        : null
      }
    </View>
  );
}