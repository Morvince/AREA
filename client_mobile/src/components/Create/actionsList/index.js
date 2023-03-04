import { View, TouchableOpacity, Text, TouchableWithoutFeedback } from 'react-native';
import DragList, {DragListRenderItemInfo} from 'react-native-draglist';
import styles from '../styles';
import { MaterialIcons } from '@expo/vector-icons';
import { white } from '../../../color';

export default function ActionsList({puzzleBlocksList, setPuzzleBlocksList, slideServicesBar, slideActionsBar, handleSlideSideBar}) {
  const heightDragList = slideActionsBar !== null ? slideServicesBar === true ? '55%' : '77%' : slideServicesBar === true ? '73%' : '77%'

  async function onReordered(fromIndex, toIndex) {
    const copy = [...puzzleBlocksList]; // Don't modify react data in-place
    const removed = copy.splice(fromIndex, 1);

    copy.splice(toIndex, 0, removed[0]); // Now insert at the new pos
    for(let i = 0; i < copy.length; i++)
      copy[i]["key"] = i
    setPuzzleBlocksList(copy);
  }

  function renderPuzzleBlocks(info) {
    const {item, onStartDrag, isActive} = info;

    return (
      <TouchableWithoutFeedback
        key={item}
        onPressIn={onStartDrag}
      >
        <View style={[styles.actionBlocks, styles.elevation, {backgroundColor: item.bgColor}]}>
          <Text style={styles.textActionBlocks}>{item.name}</Text>
          <TouchableOpacity activeOpacity={0.6} onPressOut={() => handleSlideSideBar(item)} style={styles.arrowActionBlocks}>
            <MaterialIcons name="keyboard-arrow-right" size={48} color={white}/>
          </TouchableOpacity>
        </View>
      </TouchableWithoutFeedback>
    );
  }

  return (
    <DragList
      data={puzzleBlocksList}
      keyExtractor={(item) => item.key}
      onReordered={onReordered}
      renderItem={renderPuzzleBlocks}
      containerStyle={{width: 260, height: heightDragList, alignItems: "center"}}
    />
  )
}