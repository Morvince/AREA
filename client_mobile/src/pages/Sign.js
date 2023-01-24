import React, { useCallback, useState } from "react";
import { StyleSheet, Text, View, Button } from 'react-native';
import { black, white } from "../color";
import Animated, { useAnimatedStyle, withTiming } from "react-native-reanimated";

export default function Sign({ navigation }) {
  const [slideForm, setSlideForm] = useState(0)
  const bgColor = slideForm === 0 || slideForm === 2 ? white : black

  const bgFade = useAnimatedStyle(() => {
    return {
      backgroundColor: withTiming(bgColor), //300ms duration by default
    }
  })
  const handleSlideForm = useCallback(function(event) {
    event.preventDefault()
    if (slideForm === 0)
      setSlideForm(s => s + 1)
    else if (slideForm === 1)
      setSlideForm(s => s + 1)
    else if (slideForm === 2)
      setSlideForm(s => s - 1)
  }, [slideForm])

  return (
    <Animated.View style={[styles.signPage, bgFade]}>
      <Text>Sign Screen</Text>
      {/* <Button
        title="Go to Details"
        onPress={() => navigation.navigate('Home')}
      /> */}
    </Animated.View>
  );
}

const styles = StyleSheet.create({
  signPage: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
});