import { StyleSheet, Text, View, Button, Image, ScrollView, FlatList, TouchableOpacity } from 'react-native';
import DragList, {DragListRenderItemInfo} from 'react-native-draglist';
import { Fontisto } from '@expo/vector-icons';
import { Feather } from '@expo/vector-icons';
import { useState } from 'react';
import { black, lightGray } from "../color";

{/* <Button
        title="Go to Details"
        onPress={() => navigation.navigate('Details')}
      /> */}
const SOUND_OF_SILENCE = ['hello', 'darkness', 'my', 'old', 'friend'];
export default function Home({ navigation }) {
  const [slideServicesBar, setSlideServicesBar] = useState(false)
  const [slideActionsBar, setSlideActionsBar] = useState(null)
  const [puzzleBlocksList, setPuzzleBlocksList] = useState([])
  const handleStyleActionsBar = {discord: ["#5470d6"], spotify: ["#10a143"]}

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
    setPuzzleBlocksList([...puzzleBlocksList, {key: puzzleBlocksList.length, color: handleStyleActionsBar[slideActionsBar][0]}])
  }
  const [data, setData] = useState(SOUND_OF_SILENCE);

  function keyExtractor(str) {
    return str;
  }
  async function onReordered(fromIndex, toIndex) {
    const copy = [...puzzleBlocksList]; // Don't modify react data in-place
    const removed = copy.splice(fromIndex, 1);

    copy.splice(toIndex, 0, removed[0]); // Now insert at the new pos
    setPuzzleBlocksList(copy);
  }
  function renderItem(info) {
    const {item, onStartDrag, isActive} = info;
    console.log(info)

    return (
      <TouchableOpacity
        key={item}
        onPressIn={onStartDrag}>
        <View style={[styles.draggableActionBlocks, styles.elevation, {backgroundColor: item.color}]}/>
      </TouchableOpacity>
    );
  }
  // const renderPuzzleBlocks = ({item, drag, isActive}) => {
  //   return (
  //     <ScaleDecorator>
  //       <TouchableOpacity
  //         onLongPress={drag}
  //         disabled={isActive}
  //         // style={[
  //         //   { backgroundColor: isActive ? "red" : item.backgroundColor },
  //         // ]}
  //       >
  //         <View style={[styles.draggableActionBlocks, styles.elevation, {backgroundColor: item.color}]}/>
  //       </TouchableOpacity>
  //     </ScaleDecorator>
  //   )
  // }
  return (
    <View style={styles.container}>
      <Feather name={slideServicesBar ? "arrow-down-circle" : "arrow-up-circle"} size={45} color="black" onPress={handleSlideServicesBar}
        style={[{position: 'absolute', bottom: 0}, slideServicesBar && {bottom: 80}, slideActionsBar === null ? null : {bottom: 200}, !slideServicesBar && {bottom: 0}]}
      />
      <DragList
        data={data}
        keyExtractor={keyExtractor}
        onReordered={onReordered}
        renderItem={renderItem}
      />
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
        renderItem={({item}) => <View onTouchEnd={addPuzzleBlocksToList} style={[styles.actionBlocks, styles.elevation, slideActionsBar !== null ? {backgroundColor: handleStyleActionsBar[slideActionsBar][0]} : null]}/>}
        horizontal={true}
        contentContainerStyle={{height: 120, alignItems: 'center' }}
        style={[styles.actionsSideBar, slideActionsBar === null ? {bottom: -160} : {bottom: 80}, !slideServicesBar && {bottom: -160}]}
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
    borderColor: lightGray
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
    margin: 5,
    borderRadius: 25
  },
  draggableActionBlocks: {
    width: 250,
    height: 110,
    borderRadius: 25
  },
  elevation: {
    elevation: 3,
    shadowColor: black
  },
});