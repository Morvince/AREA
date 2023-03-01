import { View } from 'react-native';
import { Feather } from '@expo/vector-icons';
import { useCallback, useState } from 'react';
import styles from '../components/Home/styles';
import ServicesBar from '../components/Home/servicesBar/index';
import ActionsBar from '../components/Home/actionsBar/index';
import ActionsList from '../components/Home/actionsList/index';
import { black } from '../color';

{/* <ButtStyleSheet, on
        title="Go to Details"
        onPress={() => navigation.navigate('Details')}
      /> */}

export default function Home({ navigation }) {
  const [slideServicesBar, setSlideServicesBar] = useState(false)
  const [slideActionsBar, setSlideActionsBar] = useState(null)
  const [puzzleBlocksList, setPuzzleBlocksList] = useState([])
  const [actionPuzzleBlock, setActionPuzzleBlock] = useState(null)
  const handleStyleActionsBar = {discord: ["#7289da", "#5470d6"], spotify: ["#1db954", "#10a143"],
                                instagram: ["#c2134f", "#e1306c"], google: ["#d92516", "#EA4335"],
                                twitter: ["#1486cc", "#1da1f2"], github: [black, "#FEFEFE"]}

  const handleSlideServicesBar = () => {
    if (slideServicesBar)
      setSlideServicesBar(false)
    else
      setSlideServicesBar(true)
  }

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
      case "instagram":
        setSlideActionsBar("instagram")
        break;
      case "google":
        setSlideActionsBar("google")
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

  const addPuzzleBlocksToList = useCallback(function() {
    actionPuzzleBlock === null ?
      setActionPuzzleBlock(<View style={[styles.actionBlocks, styles.elevation, {backgroundColor: handleStyleActionsBar[slideActionsBar][1]}]}/>) :
      setPuzzleBlocksList([...puzzleBlocksList, {key: puzzleBlocksList.length, bgColor: handleStyleActionsBar[slideActionsBar][1]}])
  }, [actionPuzzleBlock, puzzleBlocksList, slideActionsBar])

  return (
    <View style={styles.container}>
      <Feather name={slideServicesBar ? "arrow-down-circle" : "arrow-up-circle"} size={45} color="black" onPress={handleSlideServicesBar}
        style={[{position: 'absolute', bottom: 0, zIndex: 10}, slideServicesBar && {bottom: 80}, slideActionsBar === null ? null : {bottom: 205}, !slideServicesBar && {bottom: 0}]}
      />
      {actionPuzzleBlock}
      {
        actionPuzzleBlock === null ? null :
        <ActionsList puzzleBlocksList={puzzleBlocksList} setPuzzleBlocksList={setPuzzleBlocksList} slideServicesBar={slideServicesBar} slideActionsBar={slideActionsBar}/>
      }
      <ActionsBar slideServicesBar={slideServicesBar} slideActionsBar={slideActionsBar} handleStyleActionsBar={handleStyleActionsBar} addPuzzleBlocksToList={addPuzzleBlocksToList}/>
      <ServicesBar slideServicesBar={slideServicesBar} handleSlideActionsBar={handleSlideActionsBar}/>
    </View>
  );
}