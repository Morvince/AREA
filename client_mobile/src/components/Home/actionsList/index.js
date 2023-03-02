import { View, TouchableOpacity } from 'react-native';
import DragList, {DragListRenderItemInfo} from 'react-native-draglist';
import styles from '../styles';

export default function ActionsList({puzzleBlocksList, setPuzzleBlocksList, slideServicesBar, slideActionsBar}) {
  const heightDragList = slideActionsBar !== null ? '59%' : slideServicesBar === true ? '76%' : '79%'
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
    <DragList
      data={puzzleBlocksList}
      keyExtractor={(item) => item.key}
      onReordered={onReordered}
      renderItem={renderPuzzleBlocks}
      containerStyle={{width: 260, height: heightDragList, alignItems: "center"}}
    />
  )
}