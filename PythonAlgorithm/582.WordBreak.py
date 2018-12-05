'''
Given a string s and a dictionary of words dict, add spaces in s to construct a sentence where each word is a valid dictionary word.

Return all such possible sentences.

Example
Gieve s = lintcode,
dict = ["de", "ding", "co", "code", "lint"].

A solution is ["lint code", "lint co de"].'''

class Solution:
    """
    @param: s: A string
    @param: wordDict: A set of words.
    @return: All possible sentences.
    """
    def wordBreak(self, s, wordDict):
        # write your code here
        
        memo = {}
        return self.dfs(s, wordDict, memo)
        
    def dfs(self, s, dict, memo):
        if s in memo:
            return memo[s]
        
        res =[]
        
        if len(s) == 0:
            return res
        
        if s in dict:
            res.append(s)
        for i in range(1, len(s)):
            word = s[:i]
            if word not in dict:
                continue
            suffix = s[i:]
            segmentions = self.dfs(suffix, dict, memo)
            
            for segmention in segmentions:
                res.append(word + ' ' + segmention)
                
        memo[s] = res
        return res

x = Solution( )
print(x.wordBreak("lintcode",
["de", "ding", "co", "code", "lint"]))