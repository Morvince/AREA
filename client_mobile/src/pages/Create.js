import { View, Text, TouchableOpacity } from 'react-native';
import { Feather } from '@expo/vector-icons';
import { useCallback, useEffect, useState } from 'react';
import styles from '../components/Create/styles';
import ServicesBar from '../components/Create/servicesBar/index';
import ActionsBar from '../components/Create/actionsBar/index';
import ActionsList from '../components/Create/actionsList/index';
import SideBar from '../components/Create/sideBar/index';
import { useGetAllActions } from '../api/apiCreatePage';
import { MaterialIcons } from '@expo/vector-icons';
import { black, darkGray, white } from '../color';

export default function Create({ navigation }) {
  const [slideServicesBar, setSlideServicesBar] = useState(true)
  const [slideActionsBar, setSlideActionsBar] = useState(null)
  const [puzzleBlocksList, setPuzzleBlocksList] = useState([])
  const [actionPuzzleBlock, setActionPuzzleBlock] = useState(null)
  const [stateSideBar, setStateSideBar] = useState([false, null])
  const handleStyleActionsBar = {discord: ["#7289da", "#5470d6"], spotify: ["#1db954", "#10a143"],
                                twitch: ["#6713e2", "#9146ff"], gmail: ["#d92516", "#EA4335"],
                                twitter: ["#1486cc", "#1da1f2"], github: [black, darkGray]}
  const getAllActions = useGetAllActions()
  const [allActions, setAllActions] = useState(null)

  const handleSlideServicesBar = useCallback(function() {
    if (slideServicesBar)
      setSlideServicesBar(false)
    else
      setSlideServicesBar(true)
  }, [slideServicesBar])

  const handleSlideActionsBar = useCallback(function(service) {
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
  }, [slideActionsBar])

  const handleSlideSideBar = useCallback (function(item) {
    setStateSideBar(s => [!s[0], item])
  }, [])

  const addPuzzleBlocksToList = useCallback(function(item) {
    if ((actionPuzzleBlock === null && item.type === "reaction") ||
      (actionPuzzleBlock !== null && item.type === "action"))
      return
    actionPuzzleBlock === null ?
      setActionPuzzleBlock(
        <View style={[styles.actionBlocks, styles.elevation, {backgroundColor: handleStyleActionsBar[slideActionsBar][1]}]}>
          <Text style={styles.textActionBlocks}>{item.name}</Text>
          {item.fields !== null && item.fields.length !== 0 ?
            <TouchableOpacity activeOpacity={0.6} onPressOut={() => handleSlideSideBar(item)} style={styles.arrowActionBlocks}>
              <MaterialIcons name="keyboard-arrow-right" size={48} color={white}/>
            </TouchableOpacity>
            : null
          }
        </View>
      )
      : setPuzzleBlocksList([...puzzleBlocksList, {key: puzzleBlocksList.length, bgColor: handleStyleActionsBar[slideActionsBar][1], ...item}])
  }, [actionPuzzleBlock, puzzleBlocksList, slideActionsBar])

  useEffect(() => {
    getAllActions.mutate(null, {
      onSuccess: (data) => {
        setAllActions(data.data.actions)
      }
    })
  }, [])

  return (
    <View style={styles.container}>
      <Feather name={slideServicesBar ? "arrow-down-circle" : "arrow-up-circle"} size={45} color={black} onPress={handleSlideServicesBar}
        style={[{position: 'absolute', bottom: 0, zIndex: 10}, slideServicesBar && {bottom: 80}, slideActionsBar === null ? null : {bottom: 205}, !slideServicesBar && {bottom: 0}]}
      />
      {actionPuzzleBlock === null ?
        <Text style={{position: 'absolute', fontSize: 17, fontStyle: 'italic', color: darkGray, top: '50%', opacity: 0.5}}>Place your pieces here</Text>
        : null
      }
      {actionPuzzleBlock}
      {
        actionPuzzleBlock === null ? null :
        <ActionsList puzzleBlocksList={puzzleBlocksList} setPuzzleBlocksList={setPuzzleBlocksList} slideServicesBar={slideServicesBar} slideActionsBar={slideActionsBar} handleSlideSideBar={handleSlideSideBar}/>
      }
      <ActionsBar slideServicesBar={slideServicesBar} slideActionsBar={slideActionsBar} handleStyleActionsBar={handleStyleActionsBar} addPuzzleBlocksToList={addPuzzleBlocksToList} allActions={allActions} actionPuzzleBlock={actionPuzzleBlock}/>
      <ServicesBar slideServicesBar={slideServicesBar} handleSlideActionsBar={handleSlideActionsBar}/>
      <SideBar stateSideBar={stateSideBar} handleSlideSideBar={handleSlideSideBar}/>
    </View>
  );
}