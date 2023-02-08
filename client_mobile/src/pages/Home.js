import { StyleSheet, View, Image, ScrollView, FlatList, TouchableOpacity } from 'react-native';
import DragList, {DragListRenderItemInfo} from 'react-native-draglist';
import { Fontisto } from '@expo/vector-icons';
import { Feather } from '@expo/vector-icons';
import { useState } from 'react';
import { black, lightGray, white } from "../color";

{/* <Button
        title="Go to Details"
        onPress={() => navigation.navigate('Details')}
      /> */}

export default function Home({ navigation }) {
  const [slideServicesBar, setSlideServicesBar] = useState(false)
  const [slideActionsBar, setSlideActionsBar] = useState(null)
  const [puzzleBlocksList, setPuzzleBlocksList] = useState([])
  const [actionPuzzleBlock, setActionPuzzleBlock] = useState(null)
  const handleStyleActionsBar = {discord: ["#7289da", "#5470d6"], spotify: ["#1db954", "#10a143"]}

  const handleSlideServicesBar = () => {
    if (slideServicesBar)
    setSlideServicesBar(false)
    else
      setSlideServicesBar(true)
  }
  const handleSlideActionsBar = (service) => {
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
      default:
        break;
    }
  }
  const addPuzzleBlocksToList = () => {
    actionPuzzleBlock === null ?
      setActionPuzzleBlock(<View style={[styles.actionBlocks, styles.elevation, {backgroundColor: handleStyleActionsBar[slideActionsBar][1]}]}/>) :
      setPuzzleBlocksList([...puzzleBlocksList, {key: puzzleBlocksList.length, bgColor: handleStyleActionsBar[slideActionsBar][1]}])
  }
  async function onReordered(fromIndex, toIndex) {
    const copy = [...puzzleBlocksList]; // Don't modify react data in-place
    const removed = copy.splice(fromIndex, 1);

    copy.splice(toIndex, 0, removed[0]); // Now insert at the new pos
    setPuzzleBlocksList(copy);
  }
  function renderPuzzleBlocks(info) {
    const {item, onStartDrag, isActive} = info;

    return (
      <TouchableOpacity
        key={item}
        onPressIn={onStartDrag}>
        <View style={[styles.actionBlocks, styles.elevation, {backgroundColor: item.bgColor}]}/>
      </TouchableOpacity>
    );
  }
  return (
    <View style={styles.container}>
      <Feather name={slideServicesBar ? "arrow-down-circle" : "arrow-up-circle"} size={45} color="black" onPress={handleSlideServicesBar}
        style={[{position: 'absolute', bottom: 0, zIndex: 10}, slideServicesBar && {bottom: 80}, slideActionsBar === null ? null : {bottom: 205}, !slideServicesBar && {bottom: 0}]}
      />
      {actionPuzzleBlock}
      {
        actionPuzzleBlock === null ? null :
          <DragList
            data={puzzleBlocksList}
            keyExtractor={(item) => item.key}
            onReordered={onReordered}
            renderItem={renderPuzzleBlocks}
            containerStyle={{width: 260, height: '70%'}}
          />
      }
      <FlatList
        data={[
          {key: 'Discord'},
          {key: 'Spotify'},
          {key: 'Instagram'},
          {key: 'Google'},
          {key: 'Twitter'},
          {key: 'ChatGpt'},
          {key: 'John'},
          {key: 'Jillian'},
          {key: 'Jimmy'},
          {key: 'Julie'},
        ]}
        renderItem={({item}) => <View onTouchEnd={addPuzzleBlocksToList} style={[styles.actionBlocks, styles.elevation, slideActionsBar !== null ? {backgroundColor: handleStyleActionsBar[slideActionsBar][1]} : null]}/>}
        horizontal={true}
        contentContainerStyle={{height: 125, alignItems: 'center' }}
        style={[styles.actionsSideBar, slideActionsBar === null ? {bottom: -160} : {bottom: 75, backgroundColor: handleStyleActionsBar[slideActionsBar][0]}, !slideServicesBar && {bottom: -160}]}
      />
      <ScrollView contentContainerStyle={{width: '120%', height: 75, alignItems: 'center', justifyContent: 'space-between'}} horizontal={true}
      style={[styles.serviceSideBar, slideServicesBar && {bottom: 0}]}>
        <Fontisto name="discord" size={65} color="#5765f2" onPress={() => handleSlideActionsBar("discord")} />
        <Fontisto name="spotify" size={65} color="#17d860" onPress={() => handleSlideActionsBar("spotify")}/>
        <Image
          source={require('../../assets/images/instagramIcon.png')}
          fadeDuration={0}
          style={styles.serviceIcons}
        />
        <Image
          source={require('../../assets/images/googleIcon.png')}
          fadeDuration={0}
          style={styles.serviceIcons}
        />
        <Fontisto name="twitter" size={50} color="#179cf0" />
        <Image
          source={require('../../assets/images/chatgptIcon.png')}
          fadeDuration={0}
          style={styles.serviceIcons}
        />
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
    alignItems: 'center',
    justifyContent: 'center',
  },
  serviceSideBar: {
    position: 'absolute',
    bottom: -80,
    borderTopWidth: 1,
    borderColor: lightGray,
    backgroundColor: white
  },
  serviceIcons: {
    width: 65,
    height: 65
  },
  actionsSideBar: {
    position: 'absolute',
    borderTopWidth: 1,
    borderColor: lightGray
  },
  actionBlocks: {
    width: 250,
    height: 110,
    marginLeft: 5,
    marginRight: 5,
    marginTop: 1,
    marginBottom: 1,
    borderRadius: 25
  },
  elevation: {
    elevation: 5,
    shadowColor: black
  },
});